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

namespace spec\Sylius\WishlistPlugin\DependencyInjection;

use Sylius\WishlistPlugin\DependencyInjection\SyliusWishlistExtension;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class SyliusWishlistExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SyliusWishlistExtension::class);
    }

    public function it_is_instance_of_prepend_extension_interface()
    {
        $this->shouldHaveType(PrependExtensionInterface::class);
    }

    public function it_is_extending_abstract_resource_extension()
    {
        $this->shouldHaveType(AbstractResourceExtension::class);
    }
}
