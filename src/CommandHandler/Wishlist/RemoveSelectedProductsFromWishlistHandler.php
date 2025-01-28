<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsMessageHandler]
final readonly class RemoveSelectedProductsFromWishlistHandler
{
    public function __construct(
        private ProductVariantRepositoryInterface $productVariantRepository,
        private EntityManagerInterface $wishlistProductManager,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(RemoveSelectedProductsFromWishlist $removeSelectedProductsFromWishlistCommand): void
    {
        $this->removeSelectedProductsFromWishlist($removeSelectedProductsFromWishlistCommand->getWishlistProducts());

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));
    }

    private function removeSelectedProductsFromWishlist(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $this->removeProductFromWishlist($wishlistProduct);
        }
    }

    private function removeProductFromWishlist(WishlistItemInterface $wishlistItem): void
    {
        /** @var ?WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $wishlistItem->getWishlistProduct();

        if (null === $wishlistProduct) {
            throw new ResourceNotFoundException();
        }

        $productVariant = $this->productVariantRepository->find($wishlistProduct->getVariant());

        if (null === $productVariant) {
            throw new NotFoundHttpException();
        }

        $this->wishlistProductManager->remove($wishlistProduct);
    }
}
