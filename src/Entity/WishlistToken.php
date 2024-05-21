<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Entity;

use Ramsey\Uuid\Uuid;

class WishlistToken implements WishlistTokenInterface
{
    protected string $value;

    public function __construct(?string $value = null)
    {
        if (null === $value) {
            $this->value = $this->generate();
        } else {
            $this->setValue($value);
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    private function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
