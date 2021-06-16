<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command\Wishlist;

use Sylius\Component\Core\Model\ProductInterface;

class AddProductToWishlist
{
    public ProductInterface $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }
}
