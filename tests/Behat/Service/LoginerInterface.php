<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Behat\Service;

use Sylius\Component\Core\Model\ShopUserInterface;

interface LoginerInterface
{
    public function logIn(): void;

    public function logOut(): void;

    public function createUser(): ShopUserInterface;
}
