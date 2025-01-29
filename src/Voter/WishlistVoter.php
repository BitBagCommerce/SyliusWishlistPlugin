<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Voter;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class WishlistVoter extends Voter
{
    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public function __construct(
        private readonly Security $security,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        $attributes = [
            self::UPDATE,
            self::DELETE,
        ];

        return ($subject instanceof WishlistInterface || in_array($attribute, $attributes, true));
    }

    protected function voteOnAttribute(
        string         $attribute,
                       $subject,
        TokenInterface $token,
    ): bool
    {
        $user = $token->getUser();

        if (!$user instanceof ShopUserInterface) {
            $user = null;
        }

        /** @var WishlistInterface $wishlist */
        $wishlist = $subject;

        switch ($attribute) {
            case self::UPDATE:
                return $this->canUpdate($wishlist, $user);
            case self::DELETE:
                return $this->canDelete($wishlist, $user);
        }

        throw new \LogicException(sprintf('Unsupported attribute: "%s"', $attribute));
    }

    public function canUpdate(WishlistInterface $wishlist, ?ShopUserInterface $user): bool
    {
        if (!$this->security->isGranted('ROLE_USER') && null === $wishlist->getShopUser()) {
            return true;
        }

        if ($this->security->isGranted('ROLE_USER') && $wishlist->getShopUser() === $user) {
            return true;
        }

        return false;
    }

    public function canDelete(WishlistInterface $wishlist, ?ShopUserInterface $user): bool
    {
        return $this->canUpdate($wishlist, $user);
    }
}
