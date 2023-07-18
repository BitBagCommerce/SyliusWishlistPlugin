<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsv;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\ExportWishlistToCsvHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class ExportWishlistToCsvHandlerSpec extends ObjectBehavior
{
    public function let(
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvSerializerFactoryInterface $csvSerializerFactory
    ): void
    {
        $this->beConstructedWith(
            $csvWishlistProductFactory,
            $csvSerializerFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportWishlistToCsvHandler::class);
    }

    public function it_export_wishlist_to_csv(
        AddToCartCommandInterface $addToCartCommand,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        WishlistProductInterface $wishlistProduct,
        ProductInterface $product,
        WishlistItemInterface $wishlistItem,
        \SplFileObject $file,
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvWishlistProductInterface $csvWishlistProduct,
        CsvSerializerFactoryInterface $csvSerializerFactory,
        Serializer $serializer
    ): void {
        $wishlistProducts = new ArrayCollection([$wishlistItem]);

        $headers = [
            'variantId',
            'productId',
            'variantCode',
        ];

        $file->fputcsv($headers)->shouldBeCalled();

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

        $exportWishlistToCsv = new ExportWishlistToCsv($wishlistProducts, $file);

        $this->__invoke($exportWishlistToCsv);
    }
}
