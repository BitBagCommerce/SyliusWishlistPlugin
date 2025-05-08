<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\Factory;

use Dompdf\Options;
use PhpSpec\ObjectBehavior;
use Sylius\WishlistPlugin\Factory\DomPdfOptionsFactory;
use Sylius\WishlistPlugin\Factory\DomPdfOptionsFactoryInterface;

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
