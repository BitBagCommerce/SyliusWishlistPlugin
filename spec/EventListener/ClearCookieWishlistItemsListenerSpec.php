<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\EventListener\ClearCookieWishlistItemsListener;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        AdminUserInterface $adminUser,
        StorageInterface $cookieStorage
    ): void {
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($adminUser);

        $cookieStorage->set('bitbag_sylius_wishlist', null)->shouldNotBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }

    function it_adds_cookie_items_to_user_items_if_both_exist(
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        ShopUserInterface $shopUser,
        StorageInterface $cookieStorage
    ): void {
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($shopUser);

        $cookieStorage->set('bitbag_sylius_wishlist', null)->shouldBeCalled();

        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
