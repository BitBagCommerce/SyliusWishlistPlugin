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
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    private ClientInterface $client;

    private WishlistInterface $wishlist;

    private WishlistRepositoryInterface $wishlistRepository;

    private UserRepositoryInterface $userRepository;

    private ?string $token;

    private ?ShopUserInterface $user;

    public function __construct(
        ClientInterface $client,
        WishlistRepositoryInterface $wishlistRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->client = $client;
        $this->wishlistRepository = $wishlistRepository;
        $this->userRepository = $userRepository;
    }

    private function request(string $method, string $uri, string $json = '{}', $headers = null): ResponseInterface
    {
        if ($headers === null) {
            if ($method === 'PATCH') {
                $contentType = 'application/merge-patch+json';
            } else {
                $contentType = 'application/ld+json';
            }

            $headers = [
                'Accept' => 'application/ld+json',
                'Content-Type' => $contentType
            ];
        }

        if (isset($this->token)) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        return $this->client->request(
            $method,
            $uri,
            [
                'headers' => $headers,
                'body' => $json
            ]
        );
    }

    /** @Given user :email :password is authenticated */
    public function userIsAuthenticated(string $email, string $password)
    {
        $body = [
            'email' => $email,
            'password' => $password
        ];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $response = $this->request(
            'POST',
            'nginx:80/api/v2/shop/authentication-token',
            json_encode($body),
            $headers
        );

        Assert::eq($response->getStatusCode(), 200);

        $json = json_decode((string)$response->getBody());

        $this->user = $this->userRepository->findOneByEmail($email);
        $this->token = (string)$json->token;
    }

    /** @Given user has a wishlist */
    public function userHasAWishlist(): void
    {
        $response = $this->request('POST', 'nginx:80/api/v2/shop/wishlists');
        $json = json_decode((string)$response->getBody());

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int)$json->id);
        $this->wishlist = $wishlist;
    }

    /** @When user adds product :product to the wishlist */
    public function userAddsProductToTheWishlist(ProductInterface $product)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/product', $this->wishlist->getToken());
        $json = json_encode(['productId' => $product->getId()]);

        $response = $this->request('PATCH', $uri, $json);

        Assert::eq($response->getStatusCode(), 200);
    }

    /** @Then user should have product :product in the wishlist */
    public function userHasProductInTheWishlist(ProductInterface $product)
    {
        /** @var WishlistInterface $wishlist */

        if(isset($this->user)) {
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
    public function userAddsProductVariantToWishlist(ProductVariantInterface $variant)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/variant', $this->wishlist->getToken());
        $body = json_encode(['productVariantId' => $variant->getId()]);

        $response = $this->request('PATCH', $uri, $body);

        Assert::eq($response->getStatusCode(), 200);
    }

    public function userHasProductVariantInTheWishlist(ProductVariantInterface $variant)
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
}
