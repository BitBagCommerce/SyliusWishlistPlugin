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
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
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
        EntityManagerInterface $wishlistManager,
        TokenInterface $token,
        AdminUserInterface $adminUser
    ): void {
        $token->getUser()->willReturn($adminUser);

        $interactiveLoginEvent = new InteractiveLoginEvent(new Request(), $token->getWrappedObject());

        $wishlistManager->flush()->shouldNotBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_adds_cookie_items_to_user_items_if_both_exist(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $cookieWishlist,
        WishlistInterface $userWishlist,
        WishlistProductInterface $wishlistProduct,
        EntityManagerInterface $wishlistManager,
        TokenInterface $token,
        ShopUserInterface $shopUser
    ): void {
        $token->getUser()->willReturn($shopUser);
        $request = new Request();
        $request->cookies = new ParameterBag([
            'bitbag_sylius_wishlist' => 'Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId'
        ]);
        $interactiveLoginEvent = new InteractiveLoginEvent($request, $token->getWrappedObject());
        $wishlistRepository->findByToken('Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId')->willReturn($cookieWishlist);
        $wishlistRepository->findOneByShopUser($shopUser)->willReturn($userWishlist);
        $cookieWishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));

        $userWishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_associates_anon_wishlsit_with_a_user_if_user_does_not_have_one(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $cookieWishlist,
        EntityManagerInterface $wishlistManager,
        TokenInterface $token,
        ShopUserInterface $shopUser
    ): void {
        $token->getUser()->willReturn($shopUser);
        $request = new Request();
        $request->cookies = new ParameterBag([
            'bitbag_sylius_wishlist' => 'Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId'
        ]);
        $interactiveLoginEvent = new InteractiveLoginEvent($request, $token->getWrappedObject());

        $wishlistRepository->findByToken('Fq8N4W6mk12i9J2HX0U60POGG5UEzSgGW37OWd6sv2dd8FlBId')->willReturn($cookieWishlist);
        $wishlistRepository->findOneByShopUser($shopUser)->willReturn(null);

        $cookieWishlist->setShopUser($shopUser)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
