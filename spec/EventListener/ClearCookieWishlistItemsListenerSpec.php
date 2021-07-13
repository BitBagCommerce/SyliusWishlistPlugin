<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\EventListener\ClearCookieWishlistItemsListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class ClearCookieWishlistItemsListenerSpec extends ObjectBehavior
{
    public function let(StorageInterface $cookieStorage): void
    {
        $this->beConstructedWith($cookieStorage, 'bitbag_sylius_wishlist');
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ClearCookieWishlistItemsListener::class);
    }

    function it_does_nothing_if_not_shop_user(
        StorageInterface $cookieStorage,
        TokenInterface $token,
        AdminUserInterface $adminUser
    ): void {
        $token->getUser()->willReturn($adminUser);
        $interactiveLoginEvent = new InteractiveLoginEvent(new Request(), $token->getWrappedObject());
        $cookieStorage->set('bitbag_sylius_wishlist', null)->shouldNotBeCalled();
        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_adds_cookie_items_to_user_items_if_both_exist(
        StorageInterface $cookieStorage,
        TokenInterface $token,
        ShopUserInterface $shopUser
    ): void {
        $token->getUser()->willReturn($shopUser);
        $interactiveLoginEvent = new InteractiveLoginEvent(new Request(), $token->getWrappedObject());
        $cookieStorage->set('bitbag_sylius_wishlist', null)->shouldBeCalled();
        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
