<?php

namespace spec\BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\EventListener\MergeUserWishlistItemsListener;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class MergeUserWishlistItemsListenerSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        EntityManagerInterface $wishlistManager
    ): void
    {
        $this->beConstructedWith(
            $wishlistRepository,
            $wishlistFactory,
            $wishlistManager,
            'bitbag_wishlist_id'
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(MergeUserWishlistItemsListener::class);
    }

    function it_does_nothing_if_not_shop_user(
        InteractiveLoginEvent $interactiveLoginEvent,
        TokenInterface $token,
        AdminUserInterface $adminUser
    ): void
    {
        $interactiveLoginEvent->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($adminUser);

        $this->onInteractiveLogin($interactiveLoginEvent);
    }
}
