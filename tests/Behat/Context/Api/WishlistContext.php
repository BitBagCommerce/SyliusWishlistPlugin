<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    private WishlistRepositoryInterface $wishlistRepository;

    private UserRepositoryInterface $userRepository;

    private ClientInterface $client;

    private WishlistInterface $wishlist;

    private ?ShopUserInterface $user;

    private ?string $token;

    private const PATCH = 'PATCH';

    private const POST = 'POST';

    private const DELETE = 'DELETE';

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        UserRepositoryInterface $userRepository,
        ClientInterface $client
    )
    {
        $this->client = $client;
        $this->wishlistRepository = $wishlistRepository;
        $this->userRepository = $userRepository;
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
                'Content-Type' => $contentType
            ],
            'http_errors' => false
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
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/product', $wishlist->getToken());

        $body = [
            'productId' => $product->getId()
        ];

        return $this->client->request(
            self::PATCH,
            $uri,
            $this->getOptions(self::PATCH, $body)
        );
    }

    private function addProductVariantToTheWishlist(WishlistInterface $wishlist, ProductVariantInterface $variant)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/variant', $wishlist->getToken());

        $body = [
            'productVariantId' => $variant->getId()
        ];

        return $this->client->request(
            self::PATCH,
            $uri,
            $this->getOptions(self::PATCH, $body)
        );
    }

    private function removeProductFromTheWishlist(WishlistInterface $wishlist, ProductInterface $product)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/products/%s',
            $this->wishlist->getToken(),
            $product->getId()
        );

        return $this->client->request(
            self::DELETE,
            $uri,
            $this->getOptions(self::DELETE)
        );
    }

    /** @Given user :email :password is authenticated */
    public function userIsAuthenticated(string $email, string $password): void
    {
        $uri = 'nginx:80/api/v2/shop/authentication-token';

        $body = [
            'email' => $email,
            'password' => $password
        ];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = $this->client->request(
            self::POST,
            $uri,
            [
                'headers' => $headers,
                'body' => json_encode($body)
            ]
        );

        Assert::eq($response->getStatusCode(), 200);

        $json = json_decode((string)$response->getBody());

        $this->user = $this->userRepository->findOneByEmail($email);
        $this->token = (string)$json->token;
    }

    /** @Given user is unauthenticated */
    public function userIsUnauthenticated()
    {
        $this->user = null;
        $this->token = null;
    }

    /** @Given user has a wishlist */
    public function userHasAWishlist(): void
    {
        $uri = 'nginx:80/api/v2/shop/wishlists';

        $response = $this->client->request(
            self::POST,
            $uri,
            $this->getOptions(self::POST, [])
        );

        $jsonBody = json_decode((string)$response->getBody());

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int)$jsonBody->id);
        $this->wishlist = $wishlist;
    }

    /** @When user adds product :product to the wishlist */
    public function userAddsProductToTheWishlist(ProductInterface $product): void
    {
        $response = $this->addProductToTheWishlist($this->wishlist, $product);

        Assert::eq($response->getStatusCode(), 200);
    }

    /** @Then user should have product :product in the wishlist */
    public function userShouldHaveProductInTheWishlist(ProductInterface $product): bool
    {
        /** @var WishlistInterface $wishlist */

        if (isset($this->user)) {
            $wishlist = $this->wishlistRepository->findByShopUser($this->user);
        } else {
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

    /** @When user adds :variant product variant to the wishlist */
    public function userAddsProductVariantToTheWishlist(ProductVariantInterface $variant): void
    {
        $response = $this->addProductVariantToTheWishlist($this->wishlist, $variant);

        Assert::eq($response->getStatusCode(), 200);
    }

    /** @Then user should have :variant product variant in the wishlist */
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

    /** @When user removes product :product from the wishlist */
    public function userRemovesProductFromTheWishlist(ProductInterface $product)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/products/%s',
            $this->wishlist->getToken(),
            $product->getId()
        );

        $response = $this->client->request(
            self::DELETE,
            $uri,
            $this->getOptions(self::DELETE)
        );

        Assert::eq($response->getStatusCode(), 204);
    }

    /** @Then user tries to add product :product to the wishlist */
    public function userTriesToAddProductToTheWishlist(ProductInterface $product)
    {
        $response = $this->addProductToTheWishlist($this->wishlist, $product);
        $statusCode = $response->getStatusCode();

        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /** @Then user tries to add :variant product variant to the wishlist  */
    public function userTriesToAddProductVariantToTheWishlist(ProductVariantInterface $variant)
    {
        $response = $this->addProductVariantToTheWishlist($this->wishlist, $variant);
        $statusCode = $response->getStatusCode();

        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /** @Then user removes :variant product variant from the wishlist */
    public function userRemovesProductVariantFromTheWishlist(ProductVariantInterface $variant)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/productVariants/%s',
            $this->wishlist->getToken(),
            $variant->getId()
        );

        $response = $this->client->request(
            self::DELETE,
            $uri,
            $this->getOptions(self::DELETE)
        );

        Assert::eq($response->getStatusCode(), 204);
    }

    /** @Then user tries to remove product :product from the wishlist */
    public function userTriesToRemoveProductFromTheWishlist(ProductInterface $product)
    {
        $response = $this->removeProductFromTheWishlist($this->wishlist, $product);
        $statusCode = $response->getStatusCode();

        $this->resolveStatusCodeForUnauthenticatedUser($this->user, $statusCode);
    }

    /** @Then user should have an empty wishlist */
    public function userShouldHaveAnEmptyWishlist()
    {
        /** @var WishlistInterface $wishlist */

        if (isset($this->user)) {
            $wishlist = $this->wishlistRepository->findByShopUser($this->user);
        } else {
            $wishlist = $this->wishlistRepository->find($this->wishlist->getId());
        }

        Assert::eq(count($wishlist->getProducts()), 0);
    }
}
