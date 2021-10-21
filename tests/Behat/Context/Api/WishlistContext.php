<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class WishlistContext extends MinkContext implements Context
{
    protected static string $domain;

    private WishlistRepositoryInterface $wishlistRepository;

    private UserRepositoryInterface $userRepository;

    private ClientInterface $client;

    private WishlistInterface $wishlist;

    private RouterInterface $router;

    private ?ShopUserInterface $user;

    private ?string $token;

    private const PATCH = 'PATCH';

    private const POST = 'POST';

    private const DELETE = 'DELETE';

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        UserRepositoryInterface $userRepository,
        ClientInterface $client,
        RouterInterface $router
    ) {
        $this->client = $client;
        $this->wishlistRepository = $wishlistRepository;
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    /**
     * @Given user :email :password is authenticated
     *
     * @throws GuzzleException
     */
    public function userIsAuthenticated(string $email, string $password): void
    {
        $uri = $this->router->generate('sylius_api_shop_authentication_token');

        $body = [
            'email' => $email,
            'password' => $password,
        ];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $response = $this->client->request(
            self::POST,
            sprintf('%s%s', self::$domain, $uri),
            [
                'headers' => $headers,
                'body' => json_encode($body),
            ]
        );
        Assert::eq($response->getStatusCode(), 200);

        $json = json_decode((string) $response->getBody());

        $this->user = $this->userRepository->findOneByEmail($email);
        $this->token = (string) $json->token;
    }

    /**
     * @Given user is unauthenticated
     */
    public function userIsUnauthenticated(): void
    {
        $this->user = null;
        $this->token = null;
    }

    /**
     * @Given user has a wishlist
     *
     * @throws GuzzleException
     */
    public function userHasAWishlist(): void
    {
        $uri = $this->router->generate('api_wishlists_shop_create_wishlist_collection');
        $response = $this->client->request(
            self::POST,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::POST, [])
        );

        $jsonBody = json_decode((string) $response->getBody());

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int) $jsonBody->id);
        $this->wishlist = $wishlist;
    }

    /**
     * @When user adds product :product to the wishlist
     */
    public function userAddsProductToTheWishlist(ProductInterface $product): void
    {
        $response = $this->addProductToTheWishlist($this->wishlist, $product);
        Assert::eq($response->getStatusCode(), 200);
    }

    /**
     * @Then user should have product :product in the wishlist
     *
     * @throws \Exception
     */
    public function userShouldHaveProductInTheWishlist(ProductInterface $product): bool
    {
        if (isset($this->user)) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findOneByShopUser($this->user);
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->find($this->wishlist->getId());
        }

        foreach ($wishlist->getProducts() as $wishlistProduct) {
            if ($product->getId() === $wishlistProduct->getId()) {
                return true;
            }
        }

        throw new \Exception(
            sprintf('Product %s was not found in the wishlist',
                $product->getName()
            )
        );
    }

    /**
     * @When user adds :variant product variant to the wishlist
     */
    public function userAddsProductVariantToTheWishlist(ProductVariantInterface $variant): void
    {
        $response = $this->addProductVariantToTheWishlist($this->wishlist, $variant);

        Assert::eq($response->getStatusCode(), 200);
    }

    /**
     * @Then user should have :variant product variant in the wishlist
     *
     * @throws \Exception
     */
    public function userShouldHaveProductVariantInTheWishlist(ProductVariantInterface $variant): bool
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($this->wishlist->getId());

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($variant->getId() === $wishlistProduct->getVariant()->getId()) {
                return true;
            }
        }

        throw new \Exception(
            sprintf('Product variant %s was not found in the wishlist',
                $variant->getName()
            )
        );
    }

    /**
     * @When user removes product :product from the wishlist
     */
    public function userRemovesProductFromTheWishlist(ProductInterface $product): void
    {
        $uri = $this->router->generate('api_wishlists_shop_remove_product_from_wishlist_item', [
            'token' => $this->wishlist->getToken(),
            'productId' => $product->getId(),
        ]);

        $response = $this->client->request(
            self::DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::DELETE, [])
        );

        Assert::eq($response->getStatusCode(), 204);
    }

    /**
     * @When user clean the wishlist
     */
    public function userCleanWishlist(): void
    {
        $uri = $this->router->generate('api_wishlists_shop_clean_wishlist', [
            'id' => $this->wishlist->getId(),
        ]);

        $response = $this->client->request(
            self::DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::DELETE, [])
        );

        Assert::eq($response->getStatusCode(), 204);
    }

    /**
     * @Then user tries to add product :product to the wishlist
     */
    public function userTriesToAddProductToTheWishlist(ProductInterface $product): void
    {
        $response = $this->addProductToTheWishlist($this->wishlist, $product);
        $statusCode = $response->getStatusCode();
        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /**
     * @Then user tries to add :variant product variant to the wishlist
     */
    public function userTriesToAddProductVariantToTheWishlist(ProductVariantInterface $variant): void
    {
        $response = $this->addProductVariantToTheWishlist($this->wishlist, $variant);
        $statusCode = $response->getStatusCode();
        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /**
     * @Then user removes :variant product variant from the wishlist
     *
     * @throws GuzzleException
     */
    public function userRemovesProductVariantFromTheWishlist(ProductVariantInterface $variant): void
    {
        $uri = $this->router->generate('api_wishlists_shop_remove_product_variant_from_wishlist_item', [
            'token' => $this->wishlist->getToken(),
            'productVariantId' => $variant->getId(),
        ]);

        $response = $this->client->request(
            self::DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::DELETE)
        );

        Assert::eq($response->getStatusCode(), 204);
    }

    /**
     * @Then user tries to remove product :product from the wishlist
     */
    public function userTriesToRemoveProductFromTheWishlist(ProductInterface $product): void
    {
        $response = $this->removeProductFromTheWishlist($this->wishlist, $product);
        $statusCode = $response->getStatusCode();

        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /**
     * @Then user tries to clean the wishlist
     */
    public function userTriesToCleanWishlist(): void
    {
        $response = $this->cleanWishlist($this->wishlist);
        $statusCode = $response->getStatusCode();

        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /**
     * @Then user should have an empty wishlist
     */
    public function userShouldHaveAnEmptyWishlist(): void
    {
        if (isset($this->user)) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findOneByShopUser($this->user);
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->find($this->wishlist->getId());
        }

        Assert::eq(count($wishlist->getProducts()), 0);
    }

    /**
     * @BeforeScenario
     */
    public function setupDomain(): void
    {
        $domain = (string) $this->getMinkParameter('base_url');
        self::$domain = trim($domain, '/');
    }

    private function getOptions(string $method, $body = null): array
    {
        if ($method === self::PATCH) {
            $contentType = 'application/merge-patch+json';
        } else {
            $contentType = 'application/ld+json';
        }

        $options = [
            'headers' => [
                'Accept' => 'application/ld+json',
                'Content-Type' => $contentType,
            ],
        ];

        if (isset($body)) {
            $options['body'] = json_encode($body);
        }

        if (isset($this->token)) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->token;
        }

        return $options;
    }

    private function resolveStatusCodeForUnauthenticatedUser(?ShopUserInterface $user, int $statusCode): void
    {
        if (isset($user)) {
            Assert::eq($statusCode, Response::HTTP_FORBIDDEN);
        } else {
            Assert::eq($statusCode, Response::HTTP_UNAUTHORIZED);
        }
    }

    private function addProductToTheWishlist(WishlistInterface $wishlist, ProductInterface $product): ResponseInterface
    {
        $uri = $this->router->generate('api_wishlists_shop_add_product_to_wishlist_item', [
            'token' => $wishlist->getToken(),
        ]);

        $body = [
            'productId' => $product->getId(),
        ];

        return $this->client->request(
            self::PATCH,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::PATCH, $body)
        );
    }

    private function addProductVariantToTheWishlist(WishlistInterface $wishlist, ProductVariantInterface $variant): ResponseInterface
    {
        $uri = $this->router->generate('api_wishlists_shop_add_product_variant_to_wishlist_item', [
            'token' => $wishlist->getToken(),
        ]);

        $body = [
            'productVariantId' => $variant->getId(),
        ];

        return $this->client->request(
            self::PATCH,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::PATCH, $body)
        );
    }

    /**
     * @throws GuzzleException
     */
    private function removeProductFromTheWishlist(WishlistInterface $wishlist, ProductInterface $product): ResponseInterface
    {
        $uri = $this->router->generate('api_wishlists_shop_remove_product_from_wishlist_item', [
            'token' => $wishlist->getToken(),
            'productId' => $product->getId(),
        ]);

        return $this->client->request(
            self::DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::DELETE)
        );
    }

    /**
     * @throws GuzzleException
     */
    private function cleanWishlist(WishlistInterface $wishlist): ResponseInterface
    {
        $uri = $this->router->generate('api_wishlists_shop_clean_wishlist', [
            'id' => $wishlist->getId(),
        ]);

        return $this->client->request(
            self::DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(self::DELETE)
        );
    }

}
