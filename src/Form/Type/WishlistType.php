<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use BitBag\SyliusWishlistPlugin\Entity\WishlistInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WishlistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var WishlistInterface $wishlist */
        $wishlist = $options['wishlist'];

        $builder
            ->add('saveButton', SubmitType::class, [
                'label' => 'bitbag_sylius_wishlist_plugin.ui.save_wishlist',
                'attr'  => [
                    'class' => 'ui icon labeled button',
                    'icon'  => 'save',
                ],
            ])
            ->add('wishlistProducts', WishlistProductCollectionType::class, [
                'data' => $wishlist->getWishlistProducts(),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('wishlist')
            ->setAllowedTypes('wishlist', WishlistInterface::class)
            ->setDefault('data_class', null);
    }
}
