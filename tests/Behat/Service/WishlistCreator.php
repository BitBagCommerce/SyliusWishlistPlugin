<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Service;

use BitBag\SyliusWishlistPlugin\Factory\WishlistFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\WishlistProductFactoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class WishlistCreator implements WishlistCreatorInterface
{
    /** @var WishlistFactoryInterface */
    private $wishlistFactory;

    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var ProductFactoryInterface */
    private $productFactory;

    /** @var WishlistProductFactoryInterface */
    private $wishlistProductFactory;

    /** @var RepositoryInterface */
    private $wishlistRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(
        ProductFactoryInterface $productFactory,
        ChannelContextInterface $channelContext,
        WishlistFactoryInterface $wishlistFactory,
        WishlistProductFactoryInterface $wishlistProductFactory,
        RepositoryInterface $wishlistRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->productFactory = $productFactory;
        $this->channelContext = $channelContext;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistProductFactory = $wishlistProductFactory;
        $this->wishlistRepository = $wishlistRepository;
        $this->productRepository = $productRepository;
    }

    public function createWishlistWithProductAndUser(ShopUserInterface $shopUser, string $productName): void
    {
        $product = $this->createProduct($productName);
        $wishlist = $this->wishlistFactory->createForUser($shopUser);
        $wishlistProduct = $this->wishlistProductFactory->createForWishlistAndProduct($wishlist, $product);

        $wishlist->addWishlistProduct($wishlistProduct);

        $this->wishlistRepository->add($wishlist);
    }

    private function createProduct(string $name): ProductInterface
    {
        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();

        $product->setName($name);
        $product->setCode(StringInflector::nameToCode($name));
        $product->setSlug(StringInflector::nameToCode($name));
        $product->addChannel($this->channelContext->getChannel());

        $this->productRepository->add($product);

        return $product;
    }
}
