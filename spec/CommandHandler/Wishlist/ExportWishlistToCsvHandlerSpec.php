<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\CommandHandler\Wishlist;

use Sylius\WishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\CommandHandler\Wishlist\ExportWishlistToCsvHandler;
use Sylius\WishlistPlugin\Entity\WishlistProductInterface;
use Sylius\WishlistPlugin\Factory\CsvSerializerFactoryInterface;
use Sylius\WishlistPlugin\Factory\CsvWishlistProductFactoryInterface;
use Sylius\WishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Serializer;

final class ExportWishlistToCsvHandlerSpec extends ObjectBehavior
{
    public function let(
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvSerializerFactoryInterface $csvSerializerFactory,
    ): void {
        $this->beConstructedWith(
            $csvWishlistProductFactory,
            $csvSerializerFactory,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportWishlistToCsvHandler::class);
    }

    public function it_exports_wishlist_to_csv(
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        WishlistItemInterface $wishlistItem,
        WishlistProductInterface $wishlistProduct,
        ProductInterface $product,
        \SplFileObject $file,
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvWishlistProductInterface $csvWishlistProduct,
        CsvSerializerFactoryInterface $csvSerializerFactory,
        Serializer $serializer,
    ): void {
        $wishlistProducts = new ArrayCollection([$wishlistItem->getWrappedObject()]);

        $file->fputcsv(ExportWishlistToCsvHandler::CSV_HEADERS)->shouldBeCalled();

        $wishlistItem->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);

        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getId()->willReturn(1);
        $productVariant->getCode()->willReturn('test_product_variant');

        $wishlistProduct->getProduct()->willReturn($product);
        $product->getId()->willReturn(1);

        $csvWishlistProductFactory
            ->createWithProperties(1, 1, 'test_product_variant')
            ->willReturn($csvWishlistProduct)
        ;

        $csvSerializerFactory->createNew()->willReturn($serializer);
        $serializer->normalize($csvWishlistProduct, 'csv')->willReturn(['serializer_result']);

        $file->fputcsv(['serializer_result'])->shouldBeCalled();

        $exportWishlistToCsv = new ExportWishlistToCsv($wishlistProducts, $file->getWrappedObject());

        $this->__invoke($exportWishlistToCsv);
    }
}
