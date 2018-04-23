<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Controller\Action;

use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RemoveProductFromWishlistAction
{
    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var EntityManagerInterface */
    private $wishlistManager;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        WishlistContextInterface $wishlistContext,
        ProductRepositoryInterface $productRepository,
        EntityManagerInterface $wishlistManager,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->wishlistContext = $wishlistContext;
        $this->productRepository = $productRepository;
        $this->wishlistManager = $wishlistManager;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request): Response
    {
        $wishlist = $this->wishlistContext->getWishlist($request);
        /** @var ProductInterface $product */
        $product = $this->productRepository->find($request->get('productId'));

        $wishlist->removeProduct($product);
        $this->wishlistManager->flush();

        return new RedirectResponse($this->urlGenerator->generate('bitbag_sylius_wishlist_plugin_shop_wishlist_list_products'));
    }
}
