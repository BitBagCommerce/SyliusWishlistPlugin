<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class TokenUserResolver implements TokenUserResolverInterface
{
    public function resolve(?TokenInterface $token): ?UserInterface
    {
        if ($token === null) {
            return null;
        }

        $user = $token->getUser();
        if (is_string($user) && $user === 'anon.') {
            return null;
        }

        return $user;
    }
}
