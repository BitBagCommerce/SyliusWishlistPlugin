<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RemoveProductFromWishlistAction
{
    public function __construct(
        private WishlistContextInterface $wishlistContext,
        private ProductRepositoryInterface $productRepository,
        private EntityManagerInterface $wishlistProductManager,
        private RequestStack $requestStack,
        private TranslatorInterface $translator,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        /** @var ?ProductInterface $product */
        $product = $this->productRepository->find($request->get('productId'));

        if (null === $product) {
            throw new NotFoundHttpException();
        }

        /** @var WishlistInterface $wishlist */
        $wishlist = $this->wishlistContext->getWishlist($request);

        foreach ($wishlist->getWishlistProducts() as $wishlistProduct) {
            if ($product === $wishlistProduct->getProduct()) {
                $this->wishlistProductManager->remove($wishlistProduct);
            }
        }

        $this->wishlistProductManager->flush();

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add('success', $this->translator->trans('bitbag_sylius_wishlist_plugin.ui.removed_wishlist_item'));

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_locale_wishlist_list_products'));
    }
}
