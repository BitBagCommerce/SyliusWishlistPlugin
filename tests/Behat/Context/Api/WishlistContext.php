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
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    private ClientInterface $client;

    private WishlistInterface $wishlist;

    private WishlistRepositoryInterface $wishlistRepository;

    public function __construct(
        ClientInterface $client,
        WishlistRepositoryInterface $wishlistRepository
    )
    {
        $this->client = $client;
        $this->wishlistRepository = $wishlistRepository;
    }

    private function request(string $method, string $uri, string $json = '{}'): ResponseInterface
    {
        $contentType = 'application/ld+json';

        if ($method === 'PATCH') {
            $contentType = 'application/merge-patch+json';
        }

        $headers = [
            'Accept' => 'application/ld+json',
            'Content-Type' => $contentType
        ];

        return $this->client->request(
            $method,
            $uri,
            [
                'headers' => $headers,
                'body' => $json
            ]
        );
    }

    /** @Given Anonymous user has a wishlist */
    public function anonymousUserHasAWishlist(): void
    {
        $response = $this->request('POST', 'nginx:80/api/v2/shop/wishlists');
        $json = json_decode((string)$response->getBody());

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int)$json->id);
        $this->wishlist = $wishlist;
    }

    /** @When Anonymous user adds product :product to the wishlist */
    public function anonymousUserAddsProductToTheWishlist(ProductInterface $product)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/product', $this->wishlist->getToken());
        $json = json_encode(['productId' => $product->getId()]);

        $response = $this->request('PATCH', $uri, $json);

        Assert::eq($response->getStatusCode(), 200);
    }

    /** @Then Anonymous user should have product :product in the wishlist */
    public function anonymousUserHasProductInTheWishlist(ProductInterface $product)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($this->wishlist->getId());

        foreach ($wishlist->getProducts() as $wishlistProduct) {
            if ($product->getId() === $wishlistProduct->getId()) {
                return true;
            }
        }

        throw new Exception(
            sprintf('Product %s was not found in the wishlist',
                $product->getName()
            )
        );
    }

    /** @When Anonymous user adds :variant product variant to the wishlist */
    public function anonymousUserAddsProductVariantToWishlist(ProductVariantInterface $variant)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/variant', $this->wishlist->getToken());
        $body = json_encode(['productVariantId' => $variant->getId()]);

        $response = $this->request('PATCH', $uri, $body);

        Assert::eq($response->getStatusCode(), 200);
    }

    public function anonymousUserHasProductVariantInTheWishlist(ProductVariantInterface $variant)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find($this->wishlist->getId());

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($variant->getId() === $wishlistProduct->getVariant()->getId()) {
                return true;
            }
        }

        throw new Exception(
            sprintf('Product variant %s was not found in the wishlist',
                $variant->getName()
            )
        );
    }
}
