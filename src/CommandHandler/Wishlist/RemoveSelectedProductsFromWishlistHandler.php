<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlistInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RemoveSelectedProductsFromWishlistHandler implements MessageHandlerInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private EntityManagerInterface $wishlistProductManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $wishlistProductManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistProductManager = $wishlistProductManager;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function __invoke(RemoveSelectedProductsFromWishlistInterface $removeSelectedProductsFromWishlistCommand): void
    {
        $this->removeSelectedProductsFromWishlist($removeSelectedProductsFromWishlistCommand->getWishlistProducts());

        $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));
    }

    private function removeSelectedProductsFromWishlist(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            $this->removeProductFromWishlist($wishlistProduct);
        }
    }

    private function removeProductFromWishlist(WishlistItem $wishlistProduct): void
    {
        $productVariant = $this->productVariantRepository->find($wishlistProduct->getWishlistProduct()->getVariant());

        if (null === $productVariant) {
            throw new NotFoundHttpException();
        }

        $this->wishlistProductManager->remove($wishlistProduct->getWishlistProduct());
    }
}
