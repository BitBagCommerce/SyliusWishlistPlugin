<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductInStockCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCart;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdderInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    private TranslatorInterface $translator;

    private ProductInStockCheckerInterface $checker;

    private ProductToWishlistAdderInterface $adder;

    public function __construct(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder
    ) {
        $this->flashBag = $flashBag;
        $this->translator = $translator;
        $this->checker = $checker;
        $this->adder = $adder;
    }

    public function __invoke(AddSelectedProductsToCart $addSelectedProductsToCartCommand): void
    {
        $this->addSelectedProductsToCart($addSelectedProductsToCartCommand->getWishlistProducts());

        $this->flashBag->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.added_selected_wishlist_items_to_cart'));
    }

    private function addSelectedProductsToCart(Collection $wishlistProducts): void
    {
        /** @var WishlistItem $wishlistProduct */
        foreach ($wishlistProducts as $wishlistProduct) {
            if (!$this->checker->isInStock($wishlistProduct)) {
                continue;
            }

            $this->adder->addAndCheckQuantity($wishlistProduct);
        }
    }
}
