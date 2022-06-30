<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddProductToSelectedWishlist;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductToSelectedWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductToSelectedWishlistActionSpec extends ObjectBehavior
{
    public function let(
        WishlistRepositoryInterface $wishlistRepository,
        ProductRepositoryInterface $productRepository,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        MessageBusInterface $commandBus
    ): void {
        $this->beConstructedWith(
            $wishlistRepository,
            $productRepository,
            $flashBag,
            $translator,
            $urlGenerator,
            $commandBus
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductToSelectedWishlistAction::class);
    }

    public function it_throws_404_when_wishlist_is_not_found(WishlistRepositoryInterface $wishlist): void
    {
        $wishlist->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [1,1]);
    }

    public function it_throws_404_when_product_is_not_found(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        ProductRepositoryInterface $productRepository
    ): void {
        $wishlistRepository->find(1)->willReturn($wishlist);
        $productRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [1,1]);
    }

    public function it_adds_product_to_selected_wishlist(
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        MessageBusInterface $commandBus,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $wishlistRepository->find(1)->willReturn($wishlist);
        $productRepository->find(1)->willReturn($product);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('translation message');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', [
            'wishlistId' => 1,
        ])->willReturn('/wishlist/1');

        $envelope = new Envelope(new \stdClass());
        $commandBus->dispatch(Argument::type(AddProductToSelectedWishlist::class))->willReturn($envelope);
        $flashBag->add('success', 'translation message');

        $this->__invoke(1,1)->shouldHaveType(RedirectResponse::class);
    }
}
