<?php

/*
 * This file has been created by developers from Softylines.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://softylines.com and write us
 * an email on ask@softylines.com.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Exception;

final class ProuductFoundException extends Exception
{
    public function __construct(private string $productName)
    {
        parent::__construct();
    }

    public function getProductName(): string
    {
        return $this->productName;
    }
}
