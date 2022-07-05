<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlistInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveSelectedProductsFromWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RemoveSelectedProductsFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $wishlistProductManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator
    ): void
    {
        $this->beConstructedWith(
            $productVariantRepository,
            $wishlistProductManager,
            $flashBag,
            $translator
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveSelectedProductsFromWishlistHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_throws_404_when_product_variant_is_not_found(
        RemoveSelectedProductsFromWishlistInterface $removeSelectedProductsFromWishlist,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
        ProductVariantInterface $wishlistProductVariant,
        ProductVariantRepositoryInterface $productVariantRepository
    ): void
    {
        $removeSelectedProductsFromWishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistItem->getWrappedObject()]));
        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getVariant()->willReturn($wishlistProductVariant);
        $productVariantRepository->find($wishlistProductVariant)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeSelectedProductsFromWishlist]);
    }

    public function it_removes_selected_products_from_wishlist(
        RemoveSelectedProductsFromWishlistInterface $removeSelectedProductsFromWishlist,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
        ProductVariantInterface $wishlistProductVariant,
        ProductVariantInterface $productVariant,
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $wishlistProductManager,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag
    ): void
    {
        $removeSelectedProductsFromWishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistItem->getWrappedObject()]));
        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getVariant()->willReturn($wishlistProductVariant);
        $productVariantRepository->find($wishlistProductVariant)->willReturn($productVariant);
        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_selected_wishlist_items')->willReturn('translation message');

        $wishlistProductManager->remove($wishlistProduct)->shouldBeCalled();
        $flashBag->add('success', 'translation message')->shouldBeCalled();

        $this->__invoke($removeSelectedProductsFromWishlist);
    }
}
