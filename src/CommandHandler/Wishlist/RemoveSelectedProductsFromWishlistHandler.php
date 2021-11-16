<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RemoveSelectedProductsFromWishlistHandler implements MessageHandlerInterface
{
    private ProductVariantRepositoryInterface $productVariantRepository;

    private WishlistContextInterface $wishlistContext;

    private EntityManagerInterface $wishlistProductManager;

    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistContextInterface $wishlistContext,
        EntityManagerInterface $wishlistProductManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->wishlistContext = $wishlistContext;
        $this->wishlistProductManager = $wishlistProductManager;
        $this->flashBag = $flashBag;
        $this->translator = $translator;
    }

    public function __invoke(RemoveSelectedProductsFromWishlist $removeSelectedProductsFromWishlist): void
    {
        $itemsAdded = 0;

        foreach ($removeSelectedProductsFromWishlist->getWishlistProducts() as $wishlistProduct) {
            if ($wishlistProduct->isSelected()) {
                $variant = $this->productVariantRepository->find($wishlistProduct->getWishlistProduct()->getVariant());

                if (null === $variant) {
                    throw new NotFoundHttpException();
                }

                $wishlist = $removeSelectedProductsFromWishlist->getWishlist();

                foreach ($wishlist->getWishlistProducts() as $wishlistProductEntity) {
                    if ($variant === $wishlistProductEntity->getVariant()) {
                        $this->wishlistProductManager->remove($wishlistProductEntity);
                        ++$itemsAdded;
                    }
                }
            }
        }
        if (0 < $itemsAdded) {
            $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items'));
        } else {
            $this->flashBag->add('error', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products'));
        }
    }
}
