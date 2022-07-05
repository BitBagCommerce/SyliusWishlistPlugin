<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Checker\ProductInStockCheckerInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\AddSelectedProductsToCartHandler;
use BitBag\SyliusWishlistPlugin\Helper\ProductToWishlistAdderInterface;
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


}