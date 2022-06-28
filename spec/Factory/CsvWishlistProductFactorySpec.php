<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactory;
use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactoryInterface;

use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CsvWishlistProductFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CsvWishlistProductFactory::class);
        $this->shouldImplement(CsvWishlistProductFactoryInterface::class);
    }

    public function it_creates_new_csv_wishlist_product(
        CsvWishlistProductInterface $csvWishlistProduct
    ): void
    {
        $csvWishlistProduct = $this->createNew();
        $csvWishlistProduct->shouldBeAnInstanceOf(CsvWishlistProductInterface::class);
    }

    public function it_creates_new_csv_wishlist_product_with_properties(CsvWishlistProductInterface $csvWishlistProduct): void
    {
//        $csvWishlistProduct = $this->createNew();
//        $csvWishlistProduct->shouldBeAnInstanceOf(CsvWishlistProductInterface::class);

        $csvWishlistProduct->setVariantId(1)->shouldBeCalledOnce();
        $csvWishlistProduct->setProductId(1)->shouldBeCalledOnce();
        $csvWishlistProduct->setVariantCode('one')->shouldBeCalledOnce();

        $this->createWithProperties(1,1,'one')->shouldReturn($csvWishlistProduct);

    }
}


//final class CsvWishlistProductFactory implements CsvWishlistProductFactoryInterface
//{
//    public function createWithProperties(
//        int $variantId,
//        int $productId,
//        string $variantCode
//    ): CsvWishlistProductInterface {
//        /** @var CsvWishlistProductInterface $csvWishlistProduct */
//        $csvWishlistProduct = $this->createNew();
//
//        $csvWishlistProduct->setVariantId($variantId);
//        $csvWishlistProduct->setProductId($productId);
//        $csvWishlistProduct->setVariantCode($variantCode);
//
//        return $csvWishlistProduct;
//    }
//}
