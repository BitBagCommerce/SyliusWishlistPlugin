<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\ExportWishlistToCsvInterface;
use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\CommandHandler\Wishlist\ExportWishlistToCsvHandler;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Factory\CsvSerializerFactoryInterface;
use BitBag\SyliusWishlistPlugin\Factory\CsvWishlistProductFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\DTO\CsvWishlistProductInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Serializer;

final class ExportWishlistToCsvHandlerSpec extends ObjectBehavior
{
    public function let(
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvSerializerFactoryInterface $csvSerializerFactory
    ): void {
        $this->beConstructedWith(
            $csvWishlistProductFactory,
            $csvSerializerFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ExportWishlistToCsvHandler::class);
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_exports_wishlist_to_csv(
        ExportWishlistToCsvInterface $exportWishlistToCsv,
        WishlistItemInterface $wishlistItem,
        \SplFileObject $file,
        AddToCartCommandInterface $cartCommand,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        WishlistProductInterface $wishlistProduct,
        ProductInterface $product,
        CsvWishlistProductFactoryInterface $csvWishlistProductFactory,
        CsvSerializerFactoryInterface $csvSerializerFactory,
        CsvWishlistProductInterface $csvWishlistProduct,
        Serializer $serializer
    ): void {
        $exportWishlistToCsv->getWishlistProducts()
            ->willReturn(new ArrayCollection([$wishlistItem->getWrappedObject()]));
        $exportWishlistToCsv->getFile()->willReturn($file);

        $wishlistItem->getCartItem()->willReturn($cartCommand);
        $cartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getId()->willReturn(1);
        $productVariant->getCode()->willReturn('one');

        $wishlistItem->getWishlistProduct()->willReturn($wishlistProduct);
        $wishlistProduct->getProduct()->willReturn($product);
        $product->getId()->willReturn(1);

        $csvWishlistProductFactory->createWithProperties(1, 1, 'one')->willReturn($csvWishlistProduct);

        $csvSerializerFactory->createNew()->willReturn($serializer);

        $file->fputcsv([
            'variantId',
            'productId',
            'variantCode',
        ])->shouldBeCalled();

        $serializer->normalize($csvWishlistProduct, 'csv')->willReturn([1,1]);
        $file->fputcsv([1,1])->shouldBeCalled();

        $this->__invoke($exportWishlistToCsv)->shouldReturn($file);
    }
}
