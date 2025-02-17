<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusWishlistPlugin\Functional\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait UserLoginTrait
{
    protected function logInUser(string $userType, string $email): array
    {
        /** @var UserRepositoryInterface $shopUserRepository */
        $shopUserRepository = $this->get(sprintf('sylius.repository.%s_user', $userType));
        /** @var JWTTokenManagerInterface $manager */
        $manager = $this->get('lexik_jwt_authentication.jwt_manager');

        /** @var UserInterface|null $user */
        $user = $shopUserRepository->findOneByEmail($email);

        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');

        return ['HTTP_' . $authorizationHeader => 'Bearer ' . $manager->create($user)];
    }
}
