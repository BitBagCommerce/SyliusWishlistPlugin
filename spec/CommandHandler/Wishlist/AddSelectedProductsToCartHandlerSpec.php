<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductInStockCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddSelectedProductsToCartInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddSelectedProductsToCartHandler;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddSelectedProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder
    ): void
    {
        $this->beConstructedWith(
            $flashBag,
            $translator,
            $checker,
            $adder
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddSelectedProductsToCartHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_does_not_add_selected_products_to_cart_when_product_is_not_in_stock(
        AddSelectedProductsToCartInterface $addSelectedProductsToCart,
        WishlistItemInterface $wishlistProduct,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    ): void
    {
        $addSelectedProductsToCart->getWishlistProducts()
            ->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $checker->isInStock($wishlistProduct->getWrappedObject())->willReturn(false);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_selected_wishlist_items_to_cart')->willReturn('translation message');

        $flashBag->add('success', 'translation message')->shouldBeCalled();

        $this->__invoke($addSelectedProductsToCart);
    }

    public function it_adds_selected_products_to_cart(
        AddSelectedProductsToCartInterface $addSelectedProductsToCart,
        WishlistItemInterface $wishlistProduct,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    ): void
    {
        $addSelectedProductsToCart->getWishlistProducts()
            ->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $checker->isInStock($wishlistProduct->getWrappedObject())->willReturn(true);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_selected_wishlist_items_to_cart')->willReturn('translation message');

        $adder->addAndCheckQuantity($wishlistProduct->getWrappedObject())->shouldBeCalled();
        $flashBag->add('success', 'translation message')->shouldBeCalled();

        $this->__invoke($addSelectedProductsToCart);
    }
}
