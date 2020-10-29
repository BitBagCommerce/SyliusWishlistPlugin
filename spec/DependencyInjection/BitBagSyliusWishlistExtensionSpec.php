<?php

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\DependencyInjection;

use BitBag\SyliusWishlistPlugin\DependencyInjection\BitBagSyliusWishlistExtension;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

final class BitBagSyliusWishlistExtensionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(BitBagSyliusWishlistExtension::class);
    }

    function it_is_instance_of_prepend_extension_interface()
    {
        $this->shouldHaveType(PrependExtensionInterface::class);
    }

    function it_is_extending_abstract_resource_extension()
    {
        $this->shouldHaveType(AbstractResourceExtension::class);
    }
}
