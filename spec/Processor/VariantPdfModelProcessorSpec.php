<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Processor;

use Sylius\WishlistPlugin\Command\Wishlist\WishlistItem;
use Sylius\WishlistPlugin\Generator\ModelCreatorInterface;
use Sylius\WishlistPlugin\Model\VariantPdfModelInterface;
use Sylius\WishlistPlugin\Processor\VariantPdfModelProcessor;
use Sylius\WishlistPlugin\Processor\VariantPdfModelProcessorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;

final class VariantPdfModelProcessorSpec extends ObjectBehavior
{
    public function let(
        ModelCreatorInterface $pdfModelCreator,
    ) {
        $this->beConstructedWith(
            $pdfModelCreator,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(VariantPdfModelProcessor::class);
        $this->shouldImplement(VariantPdfModelProcessorInterface::class);
    }

    public function it_returns_collection_of_pdf_model(
        WishlistItem $wishlistItem,
        WishlistItem $wishlistItem2,
        VariantPdfModelInterface $pdfModel,
        VariantPdfModelInterface $pdfModel2,
        ModelCreatorInterface $pdfModelCreator,
    ): void {
        $pdfModelCreator->createWishlistItemToPdf($wishlistItem)->willReturn($pdfModel);
        $pdfModelCreator->createWishlistItemToPdf($wishlistItem2)->willReturn($pdfModel2);

        $data = new ArrayCollection([
            $wishlistItem->getWrappedObject(),
            $wishlistItem2->getWrappedObject(),
        ]);

        $this->createVariantPdfModelCollection($data)
            ->first()
            ->shouldBeLike($pdfModel);
    }

    public function it_returns_empty_collection_if_parameter_is_empty(): void
    {
        $formData = new ArrayCollection();

        $this->createVariantPdfModelCollection($formData)
            ->count()
            ->shouldBe(0);
    }
}
