<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Resolver;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface TokenUserResolverInterface
{
    public function resolve(?TokenInterface $token): ?UserInterface;
}
