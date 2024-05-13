<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactory;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class WishlistFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(WishlistFactory::class);
    }

    public function it_implements_wishlist_factory_interface(): void
    {
        $this->shouldHaveType(WishlistFactoryInterface::class);
    }

    public function it_creates_new_wishlist(FactoryInterface $factory, WishlistInterface $wishlist): void
    {
        $factory->createNew()->willReturn($wishlist);

        $this->createNew()->shouldReturn($wishlist);
    }

    public function it_creates_wishlist_for_user(
        FactoryInterface $factory,
        WishlistInterface $wishlist,
        ShopUserInterface $shopUser
    ): void {
        $factory->createNew()->willReturn($wishlist);

        $wishlist->setName('Wishlist')->shouldBeCalled();
        $wishlist->setShopUser($shopUser)->shouldBeCalled();

        $this->createForUser($shopUser)->shouldReturn($wishlist);
    }
}
