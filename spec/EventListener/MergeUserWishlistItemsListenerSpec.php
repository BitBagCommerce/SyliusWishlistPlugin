<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\EventListener\MergeUserWishlistItemsListener;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class MergeUserWishlistItemsListenerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        EntityManagerInterface $wishlistManager
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistFactory,
            $wishlistManager,
            'bitbag_sylius_wishlist'
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(MergeUserWishlistItemsListener::class);
    }

    function it_does_nothing_if_not_shop_user(
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        AdminUserInterface $adminUser,
        EntityManagerInterface $wishlistManager
    ): void {
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($adminUser);

        $wishlistManager->flush()->shouldNotBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_adds_cookie_items_to_user_items_if_both_exist(
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        Request $request,
        ParameterBag $parameterBag,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $cookieWishlist,
        WishlistInterface $userWishlist,
        WishlistProductInterface $wishlistProduct,
        EntityManagerInterface $wishlistManager
    ): void {
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);
        $interactiveLoginEvent->getRequest()->willReturn($request);
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist', '')->willReturn("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId");
        $wishlistRepository->findByToken("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId")->willReturn($cookieWishlist);
        $wishlistRepository->findByShopUser($shopUser)->willReturn($userWishlist);
        $cookieWishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));

        $userWishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_associates_anon_wishlsit_with_a_user_if_user_does_not_have_one(
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        Request $request,
        ParameterBag $parameterBag,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $cookieWishlist,
        EntityManagerInterface $wishlistManager
    ): void {
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);
        $interactiveLoginEvent->getRequest()->willReturn($request);
        $request->cookies = $parameterBag;
        $parameterBag->get('bitbag_sylius_wishlist', '')->willReturn("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId");
        $wishlistRepository->findByToken("Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId")->willReturn($cookieWishlist);
        $wishlistRepository->findByShopUser($shopUser)->willReturn(null);

        $cookieWishlist->setShopUser($shopUser)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
