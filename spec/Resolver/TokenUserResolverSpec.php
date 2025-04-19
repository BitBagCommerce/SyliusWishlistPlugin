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

namespace spec\Sylius\WishlistPlugin\Resolver;

use Sylius\WishlistPlugin\Resolver\TokenUserResolver;
use Sylius\WishlistPlugin\Resolver\TokenUserResolverInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** We cannot spec the scenario, when user is 'anon.' because of BC in Symfony 6 */
final class TokenUserResolverSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TokenUserResolver::class);
        $this->shouldImplement(TokenUserResolverInterface::class);
    }

    public function it_returns_null_for_null_token(): void
    {
        $this->resolve(null)
            ->shouldReturn(null);
    }

    public function it_returns_user_for_non_anonymous_token(
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $token->getUser()->willReturn($user);

        $this->resolve($token)
            ->shouldReturn($user);
    }
}
