<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Api;

use Behat\Behat\Context\Context;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use GuzzleHttp\ClientInterface;
use Sylius\Component\Core\Model\ProductInterface;
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

    private function getHeaders()
    {
        return [
            'Accept' => 'application/ld+json',
            'Content-Type' => 'application/ld+json'
        ];
    }

    /** @Given Anonymous user has a wishlist */
    public function anonymousUserHasAWishlist(): void
    {
        $response = $this->client->request('POST', 'nginx:80/api/v2/shop/wishlists',
            [
                'headers' => $this->getHeaders(),
                'body' => '{}'
            ]);

        $json = json_decode((string)$response->getBody());

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->find((int)$json->id);
        $this->wishlist = $wishlist;
    }

    /** @When Anonymous user adds product :product to the wishlist */
    public function anonymousUserAddsProductToTheWishlist(ProductInterface $product)
    {
        $uri = sprintf('nginx:80/api/v2/shop/wishlists/%s/product', $this->wishlist->getToken());
        $jsonBody = json_encode(['productId' => $product->getId()]);
        $headers = [
            'Accept' => 'application/ld+json',
            'Content-Type' => 'application/merge-patch+json'
        ];

        $response = $this->client->patch(
            $uri,
            [
                'headers' => $headers,
                'body' => $jsonBody
            ]);

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
            ));
    }
}
