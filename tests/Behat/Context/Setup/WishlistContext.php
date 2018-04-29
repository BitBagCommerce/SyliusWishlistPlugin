<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class WishlistContext implements Context
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var WishlistContextInterface */
    private $wishlistContext;

    /** @var WishlistProductFactoryInterface */
    private $wishlistProductFactory;

    /** @var EntityManagerInterface */
    private $wishlistManager;

    /** @var FactoryInterface */
    private $taxonFactory;

    /** @var FactoryInterface */
    private $productTaxonFactory;

    /** @var EntityManagerInterface */
    private $productTaxonManager;

    /** @var CookieSetterInterface */
    private $cookieSetter;

    /** @var string */
    private $wishlistCookieId;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistContextInterface $wishlistContext,
        WishlistProductFactoryInterface $wishlistProductFactory,
        EntityManagerInterface $wishlistManager,
        FactoryInterface $taxonFactory,
        FactoryInterface $productTaxonFactory,
        EntityManagerInterface $productTaxonManager,
        CookieSetterInterface $cookieSetter,
        string $wishlistCookieId
    )
    {
        $this->productRepository = $productRepository;
        $this->wishlistContext = $wishlistContext;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
        $this->taxonFactory = $taxonFactory;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->productTaxonManager = $productTaxonManager;
        $this->cookieSetter = $cookieSetter;
        $this->wishlistCookieId = $wishlistCookieId;
    }

    /**
     * @Given I have this product in my wishlist
     */
    public function iHaveThisProductInMyWishlist(): void
    {
        /** @var ProductInterface $product */
        $product = $this->productRepository->findOneBy([]);

        $this->addProductToWishlist($product);
    }

    /**
     * @Given I have these products in my wishlist
     */
    public function iHaveTheseProductsInMyWishlist(): void
    {
        $products = $this->productRepository->findAll();

        /** @var ProductInterface $product */
        foreach ($products as $product) {
            $this->addProductToWishlist($product);
        }
    }

    /**
     * @Given all store products appear under a main taxonomy
     */
    public function allStoreProductsAppearUnderAMainTaxonomy(): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $this->taxonFactory->createNew();
        $taxon->setCode('main');
        $taxon->setSlug('main');
        $taxon->setName('Main');

        /** @var ProductInterface $product */
        foreach ($this->productRepository->findAll() as $product) {
            /** @var ProductTaxonInterface $productTaxon */
            $productTaxon = $this->productTaxonFactory->createNew();
            $productTaxon->setTaxon($taxon);
            $productTaxon->setProduct($product);
            $product->addProductTaxon($productTaxon);

            $this->productTaxonManager->persist($taxon);
            $this->productTaxonManager->persist($productTaxon);
            $this->productTaxonManager->flush();
        }
    }

    private function addProductToWishlist(ProductInterface $product): void
    {
        $wishlist = $this->wishlistContext->getWishlist(new Request());
        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $this->wishlistProductFactory->createNew();
        $wishlistProduct->setProduct($product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        $this->cookieSetter->setCookie($this->wishlistCookieId, $wishlist->getId());
    }
}
