<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Functional\Api;

use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;
use Tests\BitBag\SyliusWishlistPlugin\Functional\FunctionalTestCase;

final class WishlistTest extends FunctionalTestCase
{
    use ShopUserLoginTrait, AdminUserLoginTrait;

    protected function setUp(): void
    {
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        $this->wishlistRepository = $this->entityManager->getRepository(Wishlist::class);

        $this->fixturesData = $this->loadFixturesFromFile('Api/WishlistTest/wishlist.yaml');
    }

    public function test_user_can_create_wishlist(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');

        $this->client->request('POST', '/api/v2/shop/wishlists', [], [], $header, json_encode([
            'tokenValue' => 'token',
            'channelCode' => 'US',
        ]));

        $response = $this->client->getResponse();

        $this->assertResponse($response, $this->getResponseDirectory('test_user_can_create_wishlist'), Response::HTTP_CREATED);
    }

    public function test_admin_can_get_wishlists(): void
    {
        $header = $this->getHeaderForLoginAdminUser('admin@example.com');

        $this->client->request('GET', '/api/v2/admin/wishlists', [], [], $header);

        $response = $this->client->getResponse();

        $this->assertResponse($response, $this->getResponseDirectory('test_admin_can_get_wishlists'), Response::HTTP_OK);
    }

    public function test_user_can_get_wishlist_items(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->fixturesData['olivier_wishlist'];
        $token = $wishlist->getToken();

        $this->client->request('GET', '/api/v2/shop/wishlists/' . $token, [], [], $header);

        $response = $this->client->getResponse();
        $this->assertResponse($response, $this->getResponseDirectory('test_user_can_get_wishlist_items'), Response::HTTP_OK);
    }

    public function test_user_can_add_product_to_wishlist(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');
        $header['CONTENT_TYPE'] = self::PATCH_TYPE;
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->fixturesData['empty_olivier_wishlist'];
        $token = $wishlist->getToken();
        /** @var ProductInterface $product */
        $product = $this->fixturesData['product_1'];

        $this->assertCount(0, $wishlist->getWishlistProducts());
        $this->client->request('PATCH', '/api/v2/shop/wishlists/' . $token . '/product', [], [], $header, json_encode([
            'productId' => $product->getId(),
            'wishlist' => $wishlist,
        ]));

        $response = $this->client->getResponse();

        /** @var ?WishlistInterface $updatedWishlist */
        $updatedWishlist = $this->wishlistRepository->findOneByToken($token);

        $this->assertNotNull($updatedWishlist);
        $this->assertCount(1, $updatedWishlist->getWishlistProducts());
        $this->assertResponse($response, $this->getResponseDirectory('test_user_can_add_product_to_wishlist'), Response::HTTP_OK);
    }

    public function test_user_can_add_variant_to_wishlist(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');
        $header['CONTENT_TYPE'] = self::PATCH_TYPE;
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->fixturesData['empty_olivier_wishlist'];
        $token = $wishlist->getToken();
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->fixturesData['product_variant_1'];

        $this->assertCount(0, $wishlist->getWishlistProducts());
        $this->client->request('PATCH', '/api/v2/shop/wishlists/' . $token . '/variant', [], [], $header, json_encode([
            'productVariantId' => $productVariant->getId(),
            'wishlist' => $wishlist,
        ]));

        $response = $this->client->getResponse();

        /** @var ?WishlistInterface $updatedWishlist */
        $updatedWishlist = $this->wishlistRepository->findOneByToken($token);

        $this->assertNotNull($updatedWishlist);
        $this->assertCount(1, $updatedWishlist->getWishlistProducts());
        $this->assertResponse($response, $this->getResponseDirectory('test_user_can_add_product_variant_to_wishlist'), Response::HTTP_OK);
    }

    public function test_user_can_delete_product_from_wishlist(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->fixturesData['olivier_wishlist'];
        $token = $wishlist->getToken();
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneByToken($token);
        /** @var ProductInterface $product */
        $product = $this->fixturesData['product_1'];

        $this->assertCount(1, $wishlist->getWishlistProducts());
        $this->client->request('DELETE', '/api/v2/shop/wishlists/' . $token . '/products/' . $product->getId(), [], [], $header);

        $response = $this->client->getResponse();

        /** @var ?WishlistInterface $updatedWishlist */
        $updatedWishlist = $this->wishlistRepository->findOneByToken($token);

        $this->assertNotNull($updatedWishlist);
        $this->assertCount(0, $updatedWishlist->getWishlistProducts());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    public function test_user_can_delete_product_variant_from_wishlist(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->fixturesData['olivier_wishlist'];
        $token = $wishlist->getToken();
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistRepository->findOneByToken($token);
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->fixturesData['product_variant_1'];

        $this->assertCount(1, $wishlist->getWishlistProducts());
        $this->client->request('DELETE', '/api/v2/shop/wishlists/' . $token . '/productVariants/' . $productVariant->getId(), [], [], $header);

        $response = $this->client->getResponse();

        /** @var ?WishlistInterface $updatedWishlist */
        $updatedWishlist = $this->wishlistRepository->findOneByToken($token);

        $this->assertNotNull($updatedWishlist);
        $this->assertCount(0, $updatedWishlist->getWishlistProducts());
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    public function test_user_can_delete_wishlist_with_token(): void
    {
        $header = $this->getHeaderForLoginShopUser('oliver@queen.com');
        /** @var WishlistInterface $wishlist */
        $wishlist = $this->fixturesData['olivier_wishlist'];
        $token = $wishlist->getToken();

        $this->assertNotNull($wishlist);
        $this->client->request('DELETE', '/api/v2/shop/wishlists/' . $token, [], [], $header);

        $response = $this->client->getResponse();

        /** @var ?WishlistInterface $updatedWishlist */
        $updatedWishlist = $this->wishlistRepository->findOneByToken($token);

        $this->assertNull($updatedWishlist);
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    private function getResponseDirectory(string $filename): string
    {
        return 'Api/WishlistTest/' . $this->getSyliusVersion(). '/' . $filename;
    }
}
