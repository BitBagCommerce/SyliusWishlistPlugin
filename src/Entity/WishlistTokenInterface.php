<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Entity;

interface WishlistTokenInterface
{
    public function getValue(): string;

    public function setValue(string $value): void;

    public function __toString(): string;
}
