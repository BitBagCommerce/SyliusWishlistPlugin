<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class AddToCartTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addToWishlist', SubmitType::class, [
                'label' => 'bitbag_sylius_wishlist_plugin.ui.add_to_wishlist',
                'attr'  => [
                    'class' => 'add-to-wishlist-button ui icon labeled button',
                ],
            ])
        ;
    }

    public function getExtendedTypes(): array
    {
        return [AddToCartType::class];
    }
}
