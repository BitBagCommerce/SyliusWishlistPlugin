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

namespace Sylius\WishlistPlugin\Voter;

use Sylius\WishlistPlugin\Entity\WishlistInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

final class WishlistVoter extends Voter
{
    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public function __construct(
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
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
        TokenInterface $token,
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
