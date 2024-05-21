<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\RemoveSelectedProductsFromWishlist;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\RemoveSelectedProductsFromWishlistHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RemoveSelectedProductsFromWishlistHandlerSpec extends ObjectBehavior
{
    public function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        EntityManagerInterface $wishlistProductManager,
        RequestStack $requestStack,
        TranslatorInterface $translator,
    ): void {
        $this->beConstructedWith(
            $productVariantRepository,
            $wishlistProductManager,
            $requestStack,
            $translator,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveSelectedProductsFromWishlistHandler::class);
    }

    public function it_removes_selected_products_from_wishlist(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
        ProductVariantInterface $productVariant,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
    ): void {
        $removeSelectedProductsCommand = new RemoveSelectedProductsFromWishlist(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $productVariant->getId()->willReturn(1);
        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getVariant()->willReturn($productVariant);
        $productVariantRepository->find($productVariant)->willReturn($productVariant);
        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);

        $this->__invoke($removeSelectedProductsCommand);
    }

    public function it_throws_exception_when_variant_not_found(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
    ): void {
        $removeSelectedProductsCommand = new RemoveSelectedProductsFromWishlist(new ArrayCollection([$wishlistItem->getWrappedObject()]));

        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getVariant()->willReturn(null);
        $productVariantRepository->find(null)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$removeSelectedProductsCommand]);
    }
}
