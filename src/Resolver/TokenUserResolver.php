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

namespace Sylius\WishlistPlugin\Resolver;

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
