<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessChecker;
use BitBag\SyliusWishlistPlugin\Checker\WishlistAccessCheckerInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductVariantToWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        WishlistAccessCheckerInterface $wishlistAccessChecker,
    ): void {
        $this->beConstructedWith(
            $productVariantRepository,
            $wishlistProductFactory,
            $requestStack,
            $translator,
            $urlGenerator,
            $wishlistRepository,
            $wishlistAccessChecker,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductVariantToWishlistAction::class);
    }

    public function it_throws_404_when_product_is_not_found(
        Request $request,
        ProductVariantRepositoryInterface $productVariantRepository,
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator,
        WishlistInterface $wishlist,
        WishlistAccessChecker $wishlistAccessChecker,
    ): void
    {
        $wishlistAccessChecker->resolveWishlist(1)->willReturn($wishlist);
        $request->get('variantId')->willReturn(1);
        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $urlGenerator->generate(Argument::any())->willReturn('localhost');
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
        WishlistAccessCheckerInterface $wishlistAccessChecker
    ): void {
        $request->get('variantId')->willReturn(1);

        $productVariantRepository->find(1)->willReturn($productVariant);

        $wishlistAccessChecker->resolveWishlist(1)->willReturn($wishlist);

        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product has been added to your wishlist.');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', ['wishlistId' => 1])->willReturn('/wishlist/1');

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
        WishlistAccessCheckerInterface $wishlistAccessChecker
    ): void {
        $request->get('variantId')->willReturn(1);
        $productVariantRepository->find(1)->willReturn($productVariant);
        $wishlistAccessChecker->resolveWishlist(1)->willReturn($wishlist);
        $wishlist->hasProductVariant($productVariant)->willReturn(false);
        $wishlistProductFactory->createForWishlistAndVariant($wishlist, $productVariant)->willReturn($wishlistProduct);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product has been added to your wishlist.');
        $urlGenerator->generate(Argument::any())->willReturn('localhost');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_show_chosen_wishlist', ['wishlistId' => 1])->willReturn('/wishlist/1');

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistRepository->add($wishlist)->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('success', 'Product has been added to your wishlist.')->shouldBeCalled();

        $this->__invoke(1, $request)->shouldHaveType(RedirectResponse::class);
    }
}
