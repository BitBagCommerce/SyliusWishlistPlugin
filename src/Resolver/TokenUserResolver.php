<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenUserResolver implements TokenUserResolverInterface
{
    public function resolve(?TokenInterface $token): ?UserInterface
    {
        if (null === $token) {
            return null;
        }

        /** @var ?UserInterface $user */
        $user = $token->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }
}
