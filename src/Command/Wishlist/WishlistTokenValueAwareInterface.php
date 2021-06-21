<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Sylius\Bundle\ApiBundle\Command\CommandAwareDataTransformerInterface;

interface WishlistTokenValueAwareInterface extends CommandAwareDataTransformerInterface
{
    public function getWishlistTokenValue(): string;

    public function setWishListTokenValue(string $token): void;
}
