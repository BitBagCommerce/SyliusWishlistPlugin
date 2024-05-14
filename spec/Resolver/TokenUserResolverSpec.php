<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusWishlistPlugin\Resolver;

use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolver;
use BitBag\SyliusWishlistPlugin\Resolver\TokenUserResolverInterface;
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
