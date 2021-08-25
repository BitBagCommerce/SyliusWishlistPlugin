<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

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
