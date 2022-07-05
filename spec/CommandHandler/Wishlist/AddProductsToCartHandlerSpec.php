<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductInStockCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductsToCartInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddProductsToCartHandler;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductsToCartHandlerSpec extends ObjectBehavior
{
    public function let(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder
    ): void {
        $this->beConstructedWith(
            $flashBag,
            $translator,
            $checker,
            $adder
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductsToCartHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_does_not_add_product_when_quantity_less_than_one(
        AddProductsToCartInterface $addProductsToCart,
        WishlistItemInterface $wishlistProduct,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        ProductInStockCheckerInterface $checker,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    ): void {
        $addProductsToCart->getWishlistProducts()
            ->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $wishlistProduct->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $checker->isInStock($wishlistProduct)->willReturn(true);
        $cartItem->getQuantity()->willReturn(0);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.increase_quantity')->willReturn('translation message');

        $flashBag->add('error', 'translation message')->shouldBeCalled();

        $this->__invoke($addProductsToCart);
    }

    public function it_does_not_add_product_when_no_product_in_stock(
        AddProductsToCartInterface $addProductsToCart,
        WishlistItemInterface $wishlistProduct,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        ProductInStockCheckerInterface $checker
    ): void {
        $addProductsToCart->getWishlistProducts()
            ->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $wishlistProduct->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $checker->isInStock($wishlistProduct)->willReturn(false);

        $this->__invoke($addProductsToCart);
    }

    public function it_adds_products_to_cart(
        AddProductsToCartInterface $addProductsToCart,
        WishlistItemInterface $wishlistProduct,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        ProductInStockCheckerInterface $checker,
        ProductToWishlistAdderInterface $adder
    ): void {
        $addProductsToCart->getWishlistProducts()
            ->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $wishlistProduct->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $checker->isInStock($wishlistProduct)->willReturn(true);
        $cartItem->getQuantity()->willReturn(1);

        $adder->addProductToWishlist($wishlistProduct)->shouldBeCalled();

        $this->__invoke($addProductsToCart);
    }
}
