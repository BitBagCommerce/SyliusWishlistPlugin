<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\EventSubscriber;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\ShopBundle\SectionResolver\ShopSection;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final readonly class LoggedUserWishlistSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SectionProviderInterface $uriBasedSectionContext,
        private WishlistsResolverInterface $wishlistsResolver,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onImplicitLogin(UserEvent $event): void
    {
        if (!$this->uriBasedSectionContext->getSection() instanceof ShopSection) {
            return;
        }

        $user = $event->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->saveWishlistsForLoggedUser($user);
    }

    public function onInteractiveLogin(InteractiveLoginEvent $interactiveLoginEvent): void
    {
        $section = $this->uriBasedSectionContext->getSection();
        if (!$section instanceof ShopSection) {
            return;
        }

        $user = $interactiveLoginEvent->getAuthenticationToken()->getUser();
        if (!$user instanceof ShopUserInterface) {
            return;
        }

        $this->saveWishlistsForLoggedUser($user);
    }

    private function saveWishlistsForLoggedUser(ShopUserInterface $user): void
    {
        $wishlists = $this->wishlistsResolver->resolve();

        if (0 === count($wishlists)) {
            return;
        }

        /** @var WishlistInterface $wishlist */
        foreach ($wishlists as $wishlist) {
            /** @var ?ShopUserInterface $wishlistShopUser */
            $wishlistShopUser = $wishlist->getShopUser();

            if (null !== $wishlistShopUser && $wishlistShopUser->getId() !== $user->getId()) {
                continue;
            }

            $wishlist->setShopUser($user);
        }

        $this->entityManager->flush();
    }
}
