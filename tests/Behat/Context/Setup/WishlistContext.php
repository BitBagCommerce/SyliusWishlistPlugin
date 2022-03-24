<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusWishlistPlugin\Context\WishlistContextInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class WishlistContext implements Context
{
    private ProductRepositoryInterface $productRepository;

    private WishlistContextInterface $wishlistContext;

    private WishlistProductFactoryInterface $wishlistProductFactory;

    private EntityManagerInterface $wishlistManager;

    private FactoryInterface $taxonFactory;

    private FactoryInterface $productTaxonFactory;

    private EntityManagerInterface $productTaxonManager;

    private CookieSetterInterface $cookieSetter;

    private string $wishlistCookieToken;

    private ChannelRepositoryInterface $channelRepository;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        WishlistContextInterface $wishlistContext,
        WishlistProductFactoryInterface $wishlistProductFactory,
        EntityManagerInterface $wishlistManager,
        FactoryInterface $taxonFactory,
        FactoryInterface $productTaxonFactory,
        EntityManagerInterface $productTaxonManager,
        CookieSetterInterface $cookieSetter,
        string $wishlistCookieToken,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->productRepository = $productRepository;
        $this->wishlistContext = $wishlistContext;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistManager = $wishlistManager;
        $this->taxonFactory = $taxonFactory;
        $this->productTaxonFactory = $productTaxonFactory;
        $this->productTaxonManager = $productTaxonManager;
        $this->cookieSetter = $cookieSetter;
        $this->wishlistCookieToken = $wishlistCookieToken;
        $this->channelRepository = $channelRepository;
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
        /** @var ?Collection $productVariants */
        $productVariants = $product->getVariants();
        $channel = $this->channelRepository->findOneByCode('WEB-US');

        $wishlistProduct->setProduct($product);
        $wishlistProduct->setVariant($productVariants->first());

        $wishlist->addWishlistProduct($wishlistProduct);
        $wishlist->setName('wishlist');
        $wishlist->setChannel($channel);

        $this->wishlistManager->persist($wishlist);
        $this->wishlistManager->flush();

        $this->cookieSetter->setCookie($this->wishlistCookieToken, $wishlist->getToken());
    }
}
