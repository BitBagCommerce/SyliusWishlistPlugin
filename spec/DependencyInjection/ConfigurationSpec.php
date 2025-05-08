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

use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class ConfigurationSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ConfigurationInterface::class);
    }

    public function it_returns_tree_builder(): void
    {
        $this->getConfigTreeBuilder()->shouldBeAnInstanceOf(TreeBuilder::class);
    }
}
