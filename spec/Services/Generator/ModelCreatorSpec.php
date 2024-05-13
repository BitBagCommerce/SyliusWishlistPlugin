<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/
declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Services\Generator;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItemInterface;
use BitBag\SyliusWishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use BitBag\SyliusWishlistPlugin\Model\VariantPdfModelInterface;
use BitBag\SyliusWishlistPlugin\Resolver\VariantImageToDataUriResolverInterface;
use BitBag\SyliusWishlistPlugin\Services\Generator\ModelCreator;
use BitBag\SyliusWishlistPlugin\Services\Generator\ModelCreatorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class ModelCreatorSpec extends ObjectBehavior
{
    public function let(
        VariantImageToDataUriResolverInterface $variantImageToDataUriResolver,
        VariantPdfModelFactoryInterface $variantPdfModelFactory,
        RequestStack $requestStack
    ): void {
        $this->beConstructedWith(
            $variantImageToDataUriResolver,
            $variantPdfModelFactory,
            $requestStack
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ModelCreator::class);
        $this->shouldImplement(ModelCreatorInterface::class);
    }

    public function it_creates_pdf_model(
        WishlistItemInterface $wishlistItem,
        OrderItemInterface $orderItem,
        AddToCartCommandInterface $addToCartCommand,
        ProductVariantInterface $variant,
        Request $request,
        VariantImageToDataUriResolverInterface $variantImageToDataUriResolver,
        VariantPdfModelFactoryInterface $variantPdfModelFactory,
        RequestStack $requestStack,
        VariantPdfModelInterface $pdfModel
    ): void {
        $wishlistItem->getCartItem()->willReturn($addToCartCommand);
        $addToCartCommand->getCartItem()->willReturn($orderItem);
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getQuantity()->willReturn(1);
        $requestStack->getCurrentRequest()->willReturn($request);
        $request->getSchemeAndHttpHost()->willReturn('host');
        $variantImageToDataUriResolver->resolve($variant, 'host')->willReturn('url');
        $variant->getCode()->willReturn('code');
        $variantPdfModelFactory->createWithVariantAndImagePath(
            $variant,
            'url',
            1,
            'code'
        )->willReturn($pdfModel);

        $this->createWishlistItemToPdf($wishlistItem)->shouldReturn($pdfModel);
    }
}
