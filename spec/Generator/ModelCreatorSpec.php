<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Generator;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistItemInterface;
use Sylius\WishlistPlugin\Generator\ModelCreator;
use Sylius\WishlistPlugin\Generator\ModelCreatorInterface;
use Sylius\WishlistPlugin\Model\Factory\VariantPdfModelFactoryInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModelInterface;
use Sylius\WishlistPlugin\Resolver\VariantImageToDataUriResolverInterface;
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
        RequestStack $requestStack,
    ): void {
        $this->beConstructedWith(
            $variantImageToDataUriResolver,
            $variantPdfModelFactory,
            $requestStack,
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
        VariantPdfModelInterface $pdfModel,
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
            'code',
        )->willReturn($pdfModel);

        $this->createWishlistItemToPdf($wishlistItem)->shouldReturn($pdfModel);
    }
}
