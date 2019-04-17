<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Entity;

use Ramsey\Uuid\Uuid;

class WishlistToken implements WishlistTokenInterface
{
    protected $value;

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

    public function __toString()
    {
        return $this->getValue();
    }

    private function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
