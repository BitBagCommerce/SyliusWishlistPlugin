<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
