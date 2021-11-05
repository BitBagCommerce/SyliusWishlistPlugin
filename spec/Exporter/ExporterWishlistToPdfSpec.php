<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Exporter;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\AddWishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use BitBag\SyliusWishlistPlugin\Exporter\ExporterWishlistToPdf;
use BitBag\SyliusWishlistPlugin\Exporter\ExporterWishlistToPdfInterface;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModel;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImagePathResolverInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Model\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class ExporterWishlistToPdfSpec extends ObjectBehavior
{
    function let(
        ProductVariantRepositoryInterface   $productVariantRepository,
        VariantImagePathResolverInterface   $variantImagePathResolver,
        VariantPdfModelFactoryInterface     $variantPdfModelFactory,
        Environment                         $twigEnvironment
    ): void
    {
        $this->beConstructedWith(
            $productVariantRepository,
            $variantImagePathResolver,
            $variantPdfModelFactory,
            $twigEnvironment
        );
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ExporterWishlistToPdf::class);
    }

    function it_implements_exporter_wishlist_to_pdf_interface(): void
    {
        $this->shouldHaveType(ExporterWishlistToPdfInterface::class);
    }

    function it_returns_false(AddWishlistProductInterface $addWishlistProduct): void
    {
        $arrayCollection = new ArrayCollection();

        $addWishlistProduct->isSelected()->willReturn(false);
        $this->handleCartItems($arrayCollection, new Request())->shouldReturn(false);
    }

    function it_throws_404_when_product_is_not_found(
        Request                             $request,
        ProductVariantRepositoryInterface   $productVariantRepository,
        AddWishlistProductInterface         $wishlistProduct,
        WishlistProductInterface            $product
    ): void
    {
        $wishlistProduct->isSelected()->willReturn(true);
        $productVariantRepository->find(null)->willReturn(null);
        $wishlistProduct->getWishlistProduct()->willReturn($product);
        $product->getVariant()->willReturn(null);

        $this
            ->shouldThrow(NotFoundHttpException::class)
            ->during('handleCartItems',[new ArrayCollection([$wishlistProduct->getWrappedObject()]),$request]);
    }

    function it_call_to_export_to_pdf_function(
        Request                             $request,
        ProductVariantRepositoryInterface   $productVariantRepository,
        ProductVariantInterface             $productVariant,
        AddWishlistProductInterface         $wishlistProduct,
        WishlistProductInterface            $product,
        AddToCartCommandInterface           $addToCartCommand,
        OrderItemInterface                  $orderItem,
        VariantImagePathResolverInterface   $variantImagePathResolver,
        VariantPdfModelFactoryInterface     $variantPdfModelFactory
    ): void
    {
        $wishlistProduct->isSelected()->willReturn(true);
        $productVariantRepository->find($productVariant)->willReturn($productVariant);
        $wishlistProduct->getWishlistProduct()->willReturn($product);
        $product->getVariant()->willReturn($productVariant);
        $wishlistProduct->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getQuantity()->willReturn(2);
        $request->getSchemeAndHttpHost()->willReturn('http://127.0.0.1:8000');
        $variantImagePathResolver
            ->resolve($productVariant,'http://127.0.0.1:8000')
            ->willReturn('http://127.0.0.1:8000/media/image/variant-0.jpg');
        $orderItem->getVariant()->willReturn($productVariant);
        $productVariant->getCode()->willReturn('variant-0');
        $variantPdfModelFactory->createWithVariantAndImagePath(
            $productVariant,
            'http://127.0.0.1:8000/media/image/variant-0.jpg',
            2,
            'variant-0'
        )->willReturn(new VariantPdfModel);

        $this->handleCartItems(
            new ArrayCollection(
                [
                    $wishlistProduct->getWrappedObject()
                ]
            ),
            $request
        )->shouldReturn(true);
    }
}