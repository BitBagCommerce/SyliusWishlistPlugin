<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Entity;

use Ramsey\Uuid\Uuid;

class WishlistToken implements WishlistTokenInterface
{
    protected string $value;

    public function __construct(?string $value = null)
    {
        if ($value === null) {
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
