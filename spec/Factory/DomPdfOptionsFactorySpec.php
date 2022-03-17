<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Factory;

use BitBag\SyliusWishlistPlugin\Factory\DomPdfOptionsFactory;
use BitBag\SyliusWishlistPlugin\Factory\DomPdfOptionsFactoryInterface;
use Dompdf\Options;
use PhpSpec\ObjectBehavior;

final class DomPdfOptionsFactorySpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DomPdfOptionsFactory::class);
        $this->shouldImplement(DomPdfOptionsFactoryInterface::class);
    }

    public function it_creates_new_dom_pdf_options(): void
    {
        $domPdfOptions = $this->createNew();
        $domPdfOptions->shouldBeAnInstanceOf(Options::class);
    }
}
