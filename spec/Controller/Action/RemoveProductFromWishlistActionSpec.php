<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Controller\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\WishlistPlugin\Context\WishlistContextInterface;
use Sylius\WishlistPlugin\Controller\Action\RemoveProductFromWishlistAction;
use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RemoveProductFromWishlistActionSpec extends ObjectBehavior
{
    public function let(
        WishlistContextInterface $wishlistContext,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $wishlistProductManager,
        RequestStack $requestStack,
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
    ): void {
        $this->beConstructedWith(
            $wishlistContext,
            $productRepository,
            $wishlistProductManager,
            $requestStack,
            $translator,
            $urlGenerator,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RemoveProductFromWishlistAction::class);
    }

    public function it_throws_404_if_product_was_not_found(Request $request, ProductRepositoryInterface $productRepository): void
    {
        $request->get('productId')->willReturn(1);
        $productRepository->find(1)->willReturn(null);

        $this->shouldThrow(NotFoundHttpException::class)->during('__invoke', [$request]);
    }

    public function it_handles_request_and_redirects_to_wishlist(
        Request $request,
        ProductRepositoryInterface $productRepository,
        ProductInterface $product,
        WishlistContextInterface $wishlistContext,
        WishlistInterface $wishlist,
        WishlistProductInterface $wishlistProduct,
        EntityManagerInterface $wishlistProductManager,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator,
        Session $session,
        FlashBagInterface $flashBag,
    ): void {
        $request->get('productId')->willReturn(1);
        $productRepository->find(1)->willReturn($product);
        $wishlistContext->getWishlist($request)->willReturn($wishlist);
        $wishlist->getWishlistProducts()->willReturn(new ArrayCollection([$wishlistProduct->getWrappedObject()]));
        $wishlistProduct->getProduct()->willReturn($product);
        $translator->trans('sylius_wishlist_plugin.ui.removed_wishlist_item')->willReturn('Product has been removed from your wishlist.');
        $urlGenerator->generate('sylius_wishlist_plugin_shop_locale_wishlist_list_products')->willReturn('/wishlist');

        $wishlistProductManager->remove($wishlistProduct)->shouldBeCalled();
        $wishlistProductManager->flush()->shouldBeCalled();

        $requestStack->getSession()->willReturn($session);
        $session->getFlashBag()->willReturn($flashBag);
        $flashBag->add('success', 'Product has been removed from your wishlist.')->shouldBeCalled();

        $this->__invoke($request)->shouldHaveType(RedirectResponse::class);
    }
}
