<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\Sylius\WishlistPlugin\DependencyInjection;

use Sylius\WishlistPlugin\DependencyInjection\BitBagSyliusWishlistExtension;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class BitBagSyliusWishlistExtensionSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BitBagSyliusWishlistExtension::class);
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
