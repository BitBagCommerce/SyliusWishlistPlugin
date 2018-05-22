<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Entity;

interface WishlistTokenInterface
{
    const VALUE_LENGTH = 50;

    public function getValue(): string;

    public function setValue(string $value): void;
}
