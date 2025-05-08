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
