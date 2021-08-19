<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventListener;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class MergeUserWishlistItemsListener
{
    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var ObjectManager */
    private $wishlistManager;

    /** @var string */
    private $wishlistCookieToken;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        ObjectManager $wishlistManager,
        string $wishlistCookieToken
    ) {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistManager = $wishlistManager;
        $this->wishlistCookieToken = $wishlistCookieToken;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->resolveWishlist($interactiveLoginEvent->getRequest(), $user);
    }

    private function resolveWishlist(Request $request, ShopUserInterface $shopUser): void
    {
        $cookieWishlistToken = $request->cookies->get($this->wishlistCookieToken, '');

        /** @var WishlistInterface|null $cookieWishlist */
        $cookieWishlist = $this->wishlistRepository->findByToken($cookieWishlistToken);

        if (null === $cookieWishlist) {
            return;
        }

        $userWishlist = $this->wishlistRepository->findOneByShopUser($shopUser);

        if (null !== $userWishlist) {
            foreach ($cookieWishlist->getWishlistProducts() as $wishlistProduct) {
                $userWishlist->addWishlistProduct($wishlistProduct);
            }
        }

        if (null === $userWishlist) {
            $cookieWishlist->setShopUser($shopUser);
        }

        $this->wishlistManager->flush();
    }
}
