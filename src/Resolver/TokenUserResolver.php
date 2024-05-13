<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Util\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenUserResolver implements TokenUserResolverInterface
{
    public function resolve(?TokenInterface $token): ?UserInterface
    {
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (is_string($user) && User::SYMFONY_5_ANON_USER === $user) {
            return null;
        }

        return $user;
    }
}
