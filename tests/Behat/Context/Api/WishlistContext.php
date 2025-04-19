<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\Sylius\WishlistPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class WishlistContext extends RawMinkContext implements Context
{
    protected static string $domain;

    private WishlistInterface $wishlist;

    private ?ShopUserInterface $user;

    private ?string $token;

    public function __construct(
        private WishlistRepositoryInterface $wishlistRepository,
        private UserRepositoryInterface $userRepository,
        private ClientInterface $client,
        private RouterInterface $router,
        private EntityManager $entityManager,
    ) {
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
            Request::METHOD_POST,
            sprintf('%s%s', self::$domain, $uri),
            [
                'headers' => $headers,
                'body' => json_encode($body),
            ],
        );
        Assert::eq($response->getStatusCode(), 200);

        $json = json_decode((string) $response->getBody());

        /** @var ?ShopUserInterface $user */
        $user = $this->userRepository->findOneByEmail($email);
        $this->user = $user;
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
            Request::METHOD_POST,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_POST, []),
        );

        Assert::eq($response->getStatusCode(), 201);
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
     * @When user adds product :product to the wishlist in :channel
     */
    public function userAddsProductToTheWishlistInChannel(ProductInterface $product, ChannelInterface $channel): void
    {
        $response = $this->addProductToTheWishlist($this->wishlist, $product, $channel);

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
            $wishlist = $this->wishlistRepository->findOneBy([
                'shopUser' => $this->user->getId(),
            ]);
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
            sprintf(
                'Product %s was not found in the wishlist',
                $product->getName(),
            ),
        );
    }

    /**
     * @Then user should have product :product in the wishlist on :channel
     *
     * @throws \Exception
     */
    public function userShouldHaveProductInTheWishlistOnChannel(ProductInterface $product, ChannelInterface $channel): bool
    {
        if (isset($this->user)) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findOneByShopUserAndChannel(
                $this->user,
                $channel,
            );
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findAllByAnonymousAndChannel(null, $channel)[0];
        }

        foreach ($wishlist->getProducts() as $wishlistProduct) {
            if ($product->getId() === $wishlistProduct->getId()) {
                return true;
            }
        }

        throw new \Exception(
            sprintf(
                'Product %s was not found in the wishlist',
                $product->getName(),
            ),
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
            /** @var ?ProductVariantInterface $wishlistProductVariant */
            $wishlistProductVariant = $wishlistProduct->getVariant();

            if (null === $wishlistProductVariant) {
                return false;
            }

            if ($variant->getId() === $wishlistProductVariant->getId()) {
                return true;
            }
        }

        throw new \Exception(
            sprintf(
                'Product variant %s was not found in the wishlist',
                $variant->getName(),
            ),
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
            Request::METHOD_DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_DELETE, []),
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
            Request::METHOD_DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_DELETE),
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
     * @Then user should have an empty wishlist
     */
    public function userShouldHaveAnEmptyWishlist(): void
    {
        if (isset($this->user)) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findOneBy([
                'shopUser' => $this->user->getId(),
            ]);
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->find($this->wishlist->getId());
        }
        $this->entityManager->refresh($wishlist);

        Assert::eq(count($wishlist->getProducts()), 0);
    }

    /**
     * @Then user should have an empty wishlist in :channel
     */
    public function userShouldHaveAnEmptyWishlistInChannel(ChannelInterface $channel): void
    {
        if (isset($this->user)) {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->findOneByShopUserAndChannel(
                $this->user,
                $channel,
            );
        } else {
            /** @var WishlistInterface $wishlist */
            $wishlist = $this->wishlistRepository->find($this->wishlist->getId());
        }
        $this->entityManager->refresh($wishlist);

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

    /**
     * @Given user has a wishlist in :channel
     *
     * @throws GuzzleException
     */
    public function userHasAWishlistInChannel(ChannelInterface $channel): void
    {
        $uri = $this->router->generate('api_wishlists_shop_create_wishlist_collection');
        $response = $this->client->request(
            Request::METHOD_POST,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_POST, ['channelCode' => $channel->getCode()]),
        );

        $jsonBody = json_decode((string) $response->getBody());

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int) $jsonBody->id);
        $this->wishlist = $wishlist;
    }

    private function getOptions(string $method, mixed $body = null): array
    {
        if (Request::METHOD_PATCH === $method) {
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

    private function addProductToTheWishlist(
        WishlistInterface $wishlist,
        ProductInterface $product,
        ChannelInterface $channel = null,
    ): ResponseInterface {
        $uri = $this->router->generate('api_wishlists_shop_add_product_to_wishlist_item', [
            'token' => $wishlist->getToken(),
        ]);

        $body = [
            'productId' => $product->getId(),
        ];

        if (null !== $channel) {
            return $this->client->request(
                Request::METHOD_PATCH,
                sprintf('%s%s?_channel_code=%s', self::$domain, $uri, $channel->getCode()),
                $this->getOptions(Request::METHOD_PATCH, $body),
            );
        }

        return $this->client->request(
            Request::METHOD_PATCH,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_PATCH, $body),
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
            Request::METHOD_PATCH,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_PATCH, $body),
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
            Request::METHOD_DELETE,
            sprintf('%s%s', self::$domain, $uri),
            $this->getOptions(Request::METHOD_DELETE),
        );
    }
}
