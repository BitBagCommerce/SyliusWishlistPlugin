<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Checker;

use BitBag\SyliusWishlistPlugin\Checker\ProductInStockChecker;
use BitBag\SyliusWishlistPlugin\Checker\ProductInStockCheckerInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductInStockCheckerSpec extends ObjectBehavior
{
    public function let(
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void
    {
        $this->beConstructedWith(
            $flashBag,
            $translator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProductInStockChecker::class);
        $this->shouldImplement(ProductInStockCheckerInterface::class);
    }

    public function it_checks_if_product_is_in_stock(
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        ProductVariantInterface $productVariant
    ): void
    {
        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $cartItem->getVariant()->willReturn($productVariant);
        $productVariant->isInStock()->willReturn(true);

        $this->isInStock($wishlistItem)->shouldReturn(true);
    }

    public function it_checks_if_product_is_not_in_stock(
        WishlistItemInterface $wishlistItem,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $cartItem,
        ProductVariantInterface $productVariant,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void
    {
        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCartItem()->willReturn($cartItem);
        $cartItem->getVariant()->willReturn($productVariant);
        $productVariant->isInStock()->willReturn(false);
        $cartItem->getProductName()->willReturn('product name');

        $message = sprintf('%s does not have sufficient stock.', 'product name');
        $translator->trans($message)->willReturn('translation message');


        $flashBag->add('error', 'translation message')->shouldBeCalled();

        $this->isInStock($wishlistItem)->shouldReturn(false);
    }
}
