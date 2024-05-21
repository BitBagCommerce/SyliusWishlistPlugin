<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Functional;

use Sylius\Bundle\CoreBundle\Application\Kernel as SyliusKernel;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Sylius\Tests\Api\Utils\ShopUserLoginTrait;

abstract class FunctionalTestCase extends JsonApiTestCase
{
    use ShopUserLoginTrait, AdminUserLoginTrait;

    public const PATCH_TYPE = 'application/merge-patch+json';

    public function __construct(
        ?string $name = null,
        array $data = [],
        string $dataName = '',
    ) {
        parent::__construct($name, $data, $dataName);

        $this->dataFixturesPath = __DIR__ . \DIRECTORY_SEPARATOR . 'DataFixtures' . \DIRECTORY_SEPARATOR . 'ORM';
        $this->expectedResponsesPath = __DIR__ . \DIRECTORY_SEPARATOR . 'Responses' . \DIRECTORY_SEPARATOR . 'Expected';
    }

    protected function getHeaderForLoginShopUser(string $email): array
    {
        $loginData = $this->logInShopUser($email);

        if (is_array($loginData)) {
            return array_merge($loginData, self::CONTENT_TYPE_HEADER);
        }

        $authorizationHeader = self::getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $loginData;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }

    protected function getHeaderForLoginAdminUser(string $email): array
    {
        $loginData = $this->logInAdminUser($email);

        if (is_array($loginData)) {
            return array_merge($loginData, self::CONTENT_TYPE_HEADER);
        }

        $authorizationHeader = self::getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $loginData;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }

    protected function getSyliusVersion(): string
    {
        return SyliusKernel::MAJOR_VERSION . '.' . SyliusKernel::MINOR_VERSION;
    }
}
