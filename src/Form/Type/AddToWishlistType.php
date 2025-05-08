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

namespace Sylius\WishlistPlugin\Form\Type;

use Sylius\WishlistPlugin\Entity\Wishlist;
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class AddToWishlistType extends AbstractType
{
    public function __construct(
        private readonly WishlistsResolverInterface $wishlistsResolver,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $wishlists = $this->wishlistsResolver->resolveAndCreate();
        if (count($wishlists) > 1) {
            $builder
                ->add('wishlists', EntityType::class, [
                    'class' => Wishlist::class,
                    'choices' => $wishlists,
                    'choice_label' => 'name',
                    'mapped' => false,
                ])
            ;
        }
    }
}
