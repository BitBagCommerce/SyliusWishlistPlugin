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
use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class AddProductToWishlistAction
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var EntityManagerInterface */
    private $wishlistManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistContextInterface $wishlistContext,
        WishlistFactoryInterface $wishlistFactory,
        EntityManagerInterface $wishlistManager
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistContext = $wishlistContext;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistManager = $wishlistManager;
    }

    public function __invoke(Request $request): Response
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->find($request->get('productId'));
        $wishlist = $this->wishlistContext->getWishlist($request);

        $wishlist->addProduct($product);

        $this->wishlistManager->flush();
    }
}
