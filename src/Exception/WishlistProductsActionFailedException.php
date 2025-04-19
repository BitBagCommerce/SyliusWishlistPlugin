<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Sylius\WishlistPlugin\Exception;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Repository\Exception\ExistingResourceException;

final class WishlistProductsActionFailedException extends ExistingResourceException
{
    private Collection $failedProductsName;

    /** @var string */
    protected $message;

    public function __construct(Collection $failedProducts, string $message)
    {
        parent::__construct();
        $this->failedProductsName = $failedProducts;
        $this->message = $message;
    }

    public function getFailedProductsName(): Collection
    {
        return $this->failedProductsName;
    }
}
