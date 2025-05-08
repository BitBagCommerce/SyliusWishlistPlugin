<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\EventSubscriber\LoggedUserWishlistSubscriber;
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LoggedUserWishlistSubscriberSpec extends ObjectBehavior
{
    public function let(
        SectionProviderInterface $uriBasedSectionContext,
        WishlistsResolverInterface $wishlistsResolver,
        EntityManagerInterface $entityManager,
    ): void {
        $this->beConstructedWith(
            $uriBasedSectionContext,
            $wishlistsResolver,
            $entityManager,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(LoggedUserWishlistSubscriber::class);
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    public function it_returns_if_invalid_section_on_implicit_login(
        SectionProviderInterface $uriBasedSectionContext,
        UserEvent $event,
        AdminSection $adminSection,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn($adminSection)->shouldBeCalledOnce();

        $this->onImplicitLogin($event);

        $event->getUser()->shouldNotHaveBeenCalled();
    }

    public function it_returns_if_invalid_user_on_implicit_login(
        SectionProviderInterface $uriBasedSectionContext,
        UserEvent $event,
        ShopSection $shopSection,
        AdminUserInterface $adminUser,
        WishlistsResolverInterface $wishlistsResolver,
    ): void {
        $uriBasedSectionContext->getSection()->willReturn($shopSection)->shouldBeCalledOnce();
        $event->getUser()->willReturn($adminUser)->shouldBeCalledOnce();

        $this->onImplicitLogin($event);

        $wishlistsResolver->resolve()->shouldNotHaveBeenCalled();
    }

    public function it_assign_shop_user_to_wishlists_without_shop_user_on_login(
        SectionProviderInterface $uriBasedSectionContext,
        UserEvent $event,
        ShopSection $shopSection,
        ShopUserInterface $shopUser,
        WishlistsResolverInterface $wishlistsResolver,
        WishlistInterface $wishlist,
        WishlistInterface $wishlist2,
        ShopUserInterface $shopUser2,
        EntityManagerInterface $entityManager,
    ): void {
        $wishlists = [
            $wishlist->getWrappedObject(),
            $wishlist2->getWrappedObject(),
        ];

        $uriBasedSectionContext->getSection()->willReturn($shopSection)->shouldBeCalledOnce();
        $event->getUser()->willReturn($shopUser)->shouldBeCalledOnce();
        $wishlistsResolver->resolve()->willReturn($wishlists)->shouldBeCalledOnce();

        $shopUser->getId()->willReturn(1)->shouldBeCalledOnce();

        $wishlist->getShopUser()->willReturn($shopUser2)->shouldBeCalledOnce();
        $shopUser2->getId()->willReturn(15)->shouldBeCalledOnce();
        $wishlist->setShopUser($shopUser)->shouldNotBeCalled();

        $wishlist2->getShopUser()->willReturn(null)->shouldBeCalledOnce();
        $wishlist2->setShopUser($shopUser)->shouldBeCalledOnce();

        $entityManager->flush()->shouldBeCalledOnce();
        $this->onImplicitLogin($event);
    }
}
