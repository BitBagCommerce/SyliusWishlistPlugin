<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\EventListener\ClearCookieWishlistItemsListener;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUser;
use Sylius\Component\Core\Model\ShopUser;
use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
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
        StorageInterface $cookieStorage
    ): void {
        $token = new AnonymousToken('TOKEN', new AdminUser());
        $interactiveLoginEvent = new InteractiveLoginEvent(new Request(), $token);
        $cookieStorage->set('bitbag_sylius_wishlist', null)->shouldNotBeCalled();
        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_adds_cookie_items_to_user_items_if_both_exist(
        StorageInterface $cookieStorage
    ): void {
        $token = new PostAuthenticationToken( new ShopUser(), 'test', []);
        $interactiveLoginEvent = new InteractiveLoginEvent(new Request(), $token);
        $cookieStorage->set('bitbag_sylius_wishlist', null)->shouldBeCalled();
        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
