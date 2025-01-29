<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Action\AddProductVariantToWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductVariantToWishlistActionSpec extends ObjectBehavior
{
    public function let(
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
    ): void {
        $this->beConstructedWith(
            $productVariantRepository,
            $wishlistProductFactory,
            $requestStack,
            $translator,
            $urlGenerator,
            $wishlistRepository,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductVariantToWishlistAction::class);
    }

    public function it_throws_404_when_wishlist_is_not_found(
        Request $request,
        WishlistRepositoryInterface $wishlistRepository,
    ): void {
        $wishlistRepository->find(1)->willReturn(null);

        $this->shouldThrow(ResourceNotFoundException::class)->during('__invoke', [1, $request]);
    }

    public function it_throws_404_when_product_is_not_found(
        Request $request,
        ProductVariantRepositoryInterface $productVariantRepository,
        WishlistRepositoryInterface $wishlistRepository,
        WishlistInterface $wishlist,
    ): void {
        $wishlistRepository->find(1)->willReturn($wishlist);
        $request->get('variantId')->willReturn(1);
        $productVariantRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [1, $request]);
    }

    public function it_handles_the_request_and_persist_new_wishlist_for_logged_shop_user(
        Request $request,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $request->get('variantId')->willReturn(1);

        $productVariantRepository->find(1)->willReturn($productVariant);

        $wishlistRepository->find(1)->willReturn($wishlist);

        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product has been added to your wishlist.');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', ['wishlistId' => 1])->willReturn('/wishlist/1');

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('success', 'Product has been added to your wishlist.')->shouldBeCalled();

        $this->__invoke(1, $request)->shouldHaveType(RedirectResponse::class);
    }

    public function it_handles_the_request_and_persist_new_wishlist_for_anonymous_user(
        Request $request,
        ProductVariantRepositoryInterface $productVariantRepository,
        ProductVariantInterface $productVariant,
        WishlistInterface $wishlist,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        WishlistRepositoryInterface $wishlistRepository,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $request->get('variantId')->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);

        $wishlistRepository->find(1)->willReturn($wishlist);

        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product has been added to your wishlist.');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_show_chosen_wishlist', ['wishlistId' => 1])->willReturn('/wishlist/1');

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('success', 'Product has been added to your wishlist.')->shouldBeCalled();

        $this->__invoke(1, $request)->shouldHaveType(RedirectResponse::class);
    }
}
