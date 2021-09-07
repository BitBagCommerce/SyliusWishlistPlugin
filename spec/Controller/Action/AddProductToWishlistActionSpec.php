<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\AddProductToWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AddProductToWishlistActionSpec extends ObjectBehavior
{
    function let(
        TokenStorageInterface $tokenStorage,
        ProductRepositoryInterface $productRepository,
        WishlistContextInterface $wishlistContext,
        WishlistProductFactoryInterface $wishlistProductFactory,
        ObjectManager $wishlistManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $this->beConstructedWith(
            $tokenStorage,
            $productRepository,
            $wishlistContext,
            $wishlistProductFactory,
            $wishlistManager,
            $flashBag,
            $translator,
            $urlGenerator,
            'bitbag_wishlist_token'
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AddProductToWishlistAction::class);
    }

    function it_throws_404_when_product_is_not_found(Request $request, ProductRepositoryInterface $productRepository): void
    {
        $request->get('productId')->willReturn(1);
        $productRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$request]);
    }

    function it_handles_the_request_and_persist_new_wishlist_for_logged_shop_user(
        Request $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        WishlistContextInterface $wishlistContext,
        WishlistInterface $wishlist,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $request->get('productId')->willReturn(1);

        $productRepository->find(1)->willReturn($product);
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProduct);
        $wishlist->getId()->willReturn(null);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product has been added to your wishlist.');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products')->willReturn('/wishlist');

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->persist($wishlist)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();
        $flashBag->add('success', 'Product has been added to your wishlist.')->shouldBeCalled();
        $wishlist->getToken()->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(RedirectResponse::class);
    }

    function it_handles_the_request_and_persist_new_wishlist_for_anonymous_user(
        Request $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        WishlistContextInterface $wishlistContext,
        WishlistInterface $wishlist,
        WishlistProductFactoryInterface $wishlistProductFactory,
        WishlistProductInterface $wishlistProduct,
        ObjectManager $wishlistManager,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $request->get('productId')->willReturn(1);
        $productRepository->find(1)->willReturn($product);
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $wishlistProductFactory->createForWishlistAndProduct($wishlist, $product)->willReturn($wishlistProduct);
        $wishlist->getId()->willReturn(null);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.added_wishlist_item')->willReturn('Product has been added to your wishlist.');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products')->willReturn('/wishlist');

        $wishlist->addWishlistProduct($wishlistProduct)->shouldBeCalled();
        $wishlistManager->persist($wishlist)->shouldBeCalled();
        $wishlistManager->flush()->shouldBeCalled();
        $flashBag->add('success', 'Product has been added to your wishlist.')->shouldBeCalled();
        $wishlist->getToken()->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(RedirectResponse::class);
    }
}
