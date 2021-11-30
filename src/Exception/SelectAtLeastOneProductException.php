<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Exception;

use Symfony\Component\Config\Definition\Exception\Exception;

final class SelectAtLeastOneProductException extends Exception
{
    protected $message = 'Select at least one product.';
}
