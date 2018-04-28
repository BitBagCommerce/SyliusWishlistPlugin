<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Controller\Action\RemoveProductFromWishlistAction;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class RemoveProductFromWishlistActionSpec extends ObjectBehavior
{
    function let(
        WishlistContextInterface $wishlistContext,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $wishlistProductManager,
        FlashBagInterface $flashBag,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $this->beConstructedWith(
            $wishlistContext,
            $productRepository,
            $wishlistProductManager,
            $flashBag,
            $translator,
            $urlGenerator
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductFromWishlistAction::class);
    }

    function it_throws_404_if_product_was_not_found(Request $request, ProductRepositoryInterface $productRepository): void
    {
        $request->get('productId')->willReturn(1);
        $productRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$request]);
    }

    function it_handles_request_and_redirects_to_wishlist(
        Request $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        WishlistContextInterface $wishlistContext,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        EntityManagerInterface $wishlistProductManager,
        TranslatorInterface $translator,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ): void {
        $request->get('productId')->willReturn(1);
        $productRepository->find(1)->willReturn($product);
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $wishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $wishlistProduct->getProduct()->willReturn($product);
        $translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_wishlist_item')->willReturn('Product has been removed from your wishlist.');
        $urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products')->willReturn('/wishlist');

        $wishlistProductManager->remove($wishlistProduct)->shouldBeCalled();
        $wishlistProductManager->flush()->shouldBeCalled();
        $flashBag->add('success', 'Product has been removed from your wishlist.')->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(RedirectResponse::class);
    }
}
