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

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class MergeUserWishlistItemsListener
{
    /** @var WishlistRepositoryInterface */
    private $wishlistRepository;

    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var EntityManagerInterface */
    private $wishlistManager;

    /** @var string */
    private $wishlistCookieId;

    public function __construct(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistFactoryInterface $wishlistFactory,
        EntityManagerInterface $wishlistManager,
        string $wishlistCookieId
    )
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistManager = $wishlistManager;
        $this->wishlistCookieId = $wishlistCookieId;
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
        $cookieWishlistId = $request->cookies->get($this->wishlistCookieId);
        /** @var WishlistInterface|null $cookieWishlist */
        $cookieWishlist = $this->wishlistRepository->find($cookieWishlistId);
        $userWishlist = $this->wishlistRepository->findByShopUser($shopUser);

        if (null === $cookieWishlist || null === $userWishlist) {
            return;
        }

        if (null !== $cookieWishlist && null !== $userWishlist) {
            foreach ($cookieWishlist->getWishlistProducts() as $wishlistProduct) {
                $userWishlist->addWishlistProduct($wishlistProduct);
            }
        }

        if (null !== $cookieWishlist && null === $userWishlist) {
            $cookieWishlist->setShopUser($shopUser);
        }

        $this->wishlistManager->flush();
    }
}
