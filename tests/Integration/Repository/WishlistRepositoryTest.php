<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Integration\Repository;

use ApiTestCase\JsonApiTestCase;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ShopUser;

final class WishlistRepositoryTest extends JsonApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $this->getContainer()->get('bitbag_sylius_wishlist_plugin.repository.wishlist');
    }

    public function testItFindsOneWishlistByShopUser(): void
    {
        $this->loadFixturesFromFile('test_it_finds_one_wishlist_by_shop_user.yaml');

        $shopUser = $this->entityManager->getRepository(ShopUser::class)->findOneByEmail('oliver@queen.com');
        /** @var ?WishlistInterface $result */
        $result = $this->repository->findOneByShopUser($shopUser);

        $this->assertNotNull($result);
        $this->assertCount(1, [$result]);
        $this->assertSame('Olivier Wishlist', $result->getName());
        $this->assertSame($shopUser, $result->getShopUser());
    }

    public function testItFindsOneWishlistByToken(): void
    {
        $this->loadFixturesFromFile('test_it_finds_one_wishlist_by_token.yaml');

        /** @var ?WishlistInterface $result */
        $result = $this->repository->findByToken('token');

        $this->assertNotNull($result);
        $this->assertCount(1, [$result]);
        $this->assertSame('Wishlist One', $result->getName());
    }

    public function testItFindsAllWishlistsByToken(): void
    {
        $this->loadFixturesFromFile('test_it_finds_all_wishlists_by_token.yaml');

        /** @var array $result */
        $result = $this->repository->findAllByToken('token');

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('Wishlist One', $result[0]->getName());
        $this->assertSame('Wishlist Two', $result[1]->getName());
    }

    public function testItFindsAllWishlistsByShopUser(): void
    {
        $this->loadFixturesFromFile('test_it_finds_all_wishlists_by_shop_user.yaml');

        $shopUser = $this->entityManager->getRepository(ShopUser::class)->findOneByEmail('oliver@queen.com');
        /** @var array $result */
        $result = $this->repository->findAllByShopUser($shopUser->getId());

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('Olivier Wishlist', $result[0]->getName());
        $this->assertSame('Olivier Wishlist 2', $result[1]->getName());
        $this->assertSame($shopUser, $result[0]->getShopUser());
        $this->assertSame($shopUser, $result[1]->getShopUser());
    }

    public function testItFindsAllWishlistsByShopUserAndToken(): void
    {
        $this->loadFixturesFromFile('test_it_finds_one_wishlist_by_shop_user_and_token.yaml');

        $shopUser = $this->entityManager->getRepository(ShopUser::class)->findOneByEmail('oliver@queen.com');
        /** @var array $result */
        $result = $this->repository->findAllByShopUserAndToken($shopUser->getId(), 'olivier_token');

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertSame('Olivier Wishlist', $result[0]->getName());
        $this->assertSame('Olivier Wishlist 2', $result[1]->getName());
        $this->assertSame($shopUser, $result[0]->getShopUser());
        $this->assertSame($shopUser, $result[1]->getShopUser());
        $this->assertSame('Olivier token wishlist', $result[2]->getName());
        $this->assertSame('Olivier token wishlist 2', $result[3]->getName());
        $this->assertSame('olivier_token', $result[3]->getToken());
        $this->assertSame('olivier_token', $result[3]->getToken());
    }

    public function testItFindsAllAnonymousWishlists(): void
    {
        $this->loadFixturesFromFile('test_it_finds_all_anonymous_wishlists.yaml');

        /** @var array $result */
        $result = $this->repository->findAllByAnonymous('token');

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('Wishlist One', $result[0]->getName());
        $this->assertSame('Wishlist Two', $result[1]->getName());
    }

    public function testItFindsOneWishlistsByShopUserAndChannel(): void
    {
        $this->loadFixturesFromFile('test_it_finds_one_wishlist_by_shop_user_and_channel.yaml');

        $shopUser = $this->entityManager->getRepository(ShopUser::class)->findOneByEmail('oliver@queen.com');
        $channel = $this->entityManager->getRepository(Channel::class)->findOneBy(['name' => 'name']);
        /** @var ?WishlistInterface $result */
        $result = $this->repository->findOneByShopUserAndChannel($shopUser, $channel);

        $this->assertNotNull($result);
        $this->assertInstanceOf(WishlistInterface::class, $result);
        $this->assertCount(1, [$result]);
        $this->assertSame('Olivier Wishlist', $result->getName());
        $this->assertSame($shopUser, $result->getShopUser());
    }

    public function testItFindsAllAnonymousWishlistsWithChannel(): void
    {
        $this->loadFixturesFromFile('test_it_finds_all_anonymous_wishlists_with_channel.yaml');

        $channel = $this->entityManager->getRepository(Channel::class)->findOneBy(['name' => 'name']);
        /** @var array $result */
        $result = $this->repository->findAllByAnonymousAndChannel('token', $channel);

        $this->assertNotNull($result);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('Wishlist One', $result[0]->getName());
        $this->assertSame('Wishlist Two', $result[1]->getName());
    }

    public function testItFindsOneWishlistByTokenAndName(): void
    {
        $this->loadFixturesFromFile('test_it_finds_one_wishlist_by_token_and_name.yaml');

        /** @var ?WishlistInterface $result */
        $result = $this->repository->findOneByTokenAndName('token', 'Olivier Wishlist');
        /** @var ?WishlistInterface $result */
        $this->assertNotNull($result);
        $this->assertInstanceOf(WishlistInterface::class, $result);
        $this->assertCount(1, [$result]);
        $this->assertSame('Olivier Wishlist', $result->getName());

        $missingResult = $this->repository->findOneByTokenAndName('non_existing_token', 'Olivier Wishlist');
        $this->assertNull($missingResult);
    }

    public function testItFindsOneWishlistByShopUserAndName(): void
    {
        $this->loadFixturesFromFile('test_it_finds_one_wishlist_by_shop_user_and_name.yaml');

        $shopUser = $this->entityManager->getRepository(ShopUser::class)->findOneByEmail('oliver@queen.com');

        /** @var ?WishlistInterface $result */
        $result = $this->repository->findOneByShopUserAndName($shopUser, 'Olivier Wishlist');
        /** @var ?WishlistInterface $result */
        $this->assertNotNull($result);
        $this->assertInstanceOf(WishlistInterface::class, $result);
        $this->assertCount(1, [$result]);
        $this->assertSame('Olivier Wishlist', $result->getName());

        $missingResult = $this->repository->findOneByShopUserAndName($shopUser, 'Bruce Wishlist');
        $this->assertNull($missingResult);
    }

    public function testItDeleteAllAnonymousUntilWishlists(): void
    {
        $this->loadFixturesFromFile('test_it_delete_all_anonymous_until_wishlists.yaml');

        /** @var int $result */
        $result = $this->repository->deleteAllAnonymousUntil(new \DateTime('2024-01-01 00:00:00'));

        $this->assertIsInt($result);
        $this->assertSame(1, $result);

        $wishlists = $this->repository->findAll();
        $this->assertCount(2, $wishlists);
        $this->assertSame('Wishlist Two', $wishlists[0]->getName());
        $this->assertSame('Olivier Wishlist', $wishlists[1]->getName());
    }
}
