<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Voter;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

final class WishlistVoter extends Voter
{
    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public function __construct(
        private Security $security
    ) {}

    protected function supports($attribute, $subject): bool
    {
        $attributes = [
            self::UPDATE,
            self::DELETE,
        ];

        if (!in_array($attribute, $attributes, true) ||
            !$subject instanceof WishlistInterface) {
            return false;
        }

        return true;
    }

    /** @param string $attribute */
    protected function voteOnAttribute(
        $attribute,
        $subject,
        TokenInterface $token
    ): bool {
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
