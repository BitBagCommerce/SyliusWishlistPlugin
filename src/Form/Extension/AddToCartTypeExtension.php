<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Extension;

use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use BitBag\SyliusWishlistPlugin\Resolver\WishlistsResolverInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private WishlistsResolverInterface $wishlistsResolver
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['is_wishlist']) {
            $builder
                ->add('addToWishlist', SubmitType::class, [
                    'label' => 'bitbag_sylius_wishlist_plugin.ui.add_to_wishlist',
                    'attr' => [
                        'class' => 'bitbag-add-variant-to-wishlist ui icon labeled button',
                    ],
                ])
                ->add('wishlists', EntityType::class, [
                    'class' => Wishlist::class,
                    'choices' => $this->wishlistsResolver->resolve(),
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
