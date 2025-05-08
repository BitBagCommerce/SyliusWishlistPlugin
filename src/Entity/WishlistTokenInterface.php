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

namespace Sylius\WishlistPlugin\Entity;

interface WishlistTokenInterface
{
    public function getValue(): string;

    public function setValue(string $value): void;

    public function __toString(): string;
}
