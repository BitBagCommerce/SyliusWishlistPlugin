<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventListener;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Storage\StorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class ClearCookieWishlistItemsListener
{
    private StorageInterface $cookieStorage;

    private string $wishlistCookieToken;

    public function __construct(StorageInterface $cookieStorage, string $wishlistCookieToken)
    {
        $this->cookieStorage = $cookieStorage;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->cookieStorage->set($this->wishlistCookieToken, null);
    }
}
