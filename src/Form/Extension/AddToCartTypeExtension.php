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

namespace Sylius\WishlistPlugin\Form\Extension;

use Sylius\WishlistPlugin\Entity\Wishlist;
use Sylius\WishlistPlugin\Resolver\WishlistsResolverInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private WishlistsResolverInterface $wishlistsResolver,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isWishlist = (bool) $options['is_wishlist'];

        if (false === $isWishlist) {
            $builder
                ->add('addToWishlist', SubmitType::class, [
                    'label' => 'sylius_wishlist_plugin.ui.add_to_wishlist',
                    'attr' => [
                        'class' => 'bitbag-add-variant-to-wishlist ui icon labeled button',
                    ],
                ])
                ->add('wishlists', EntityType::class, [
                    'class' => Wishlist::class,
                    'choices' => $this->wishlistsResolver->resolveAndCreate(),
                    'choice_label' => 'name',
                    'mapped' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('is_wishlist', false)
            ->setAllowedTypes('is_wishlist', 'bool')
        ;
    }

    public function getExtendedType(): string
    {
        return AddToCartType::class;
    }

    public static function getExtendedTypes(): array
    {
        return [AddToCartType::class];
    }
}
