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

namespace Tests\Sylius\WishlistPlugin\Behat\Service;

use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class Loginer implements LoginerInterface
{
    private const EXAMPLE_USER_LOGIN = 'shop@example.com';

    private const EXAMPLE_USER_PASSWORD = '123';

    public function __construct(
        private FactoryInterface $customerFactory,
        private FactoryInterface $shopUserFactory,
        private RepositoryInterface $shopUserRepository,
        private LoginPageInterface $loginPage,
        private HomePageInterface $homePage,
    ) {
    }

    public function logIn(): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername(self::EXAMPLE_USER_LOGIN);
        $this->loginPage->specifyPassword(self::EXAMPLE_USER_PASSWORD);
        $this->loginPage->logIn();
    }

    public function logOut(): void
    {
        $this->homePage->logOut();
    }

    public function createUser(): ShopUserInterface
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerFactory->createNew();
        $customer->setEmail(self::EXAMPLE_USER_LOGIN);
        $customer->setFirstName('Johnnie');
        $customer->setLastName('Walker');

        /** @var ShopUserInterface $shopUser */
        $shopUser = $this->shopUserFactory->createNew();
        $shopUser->setPlainPassword(self::EXAMPLE_USER_PASSWORD);
        $shopUser->setEnabled(true);
        $shopUser->addRole('ROLE_USER');
        $shopUser->setCustomer($customer);

        $this->shopUserRepository->add($shopUser);

        return $shopUser;
    }
}
