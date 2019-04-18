<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use BitBag\SyliusWishlistPlugin\Entity\WishlistProduct;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantMatchType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WishlistProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var WishlistProductInterface $wishlistProduct */
        $wishlistProduct = $options['wishlistProduct'];

        $wishlistProduct->getProduct()->getVariantSelectionMethod();

        $builder
            ->add('variant', $this->getVariantType($wishlistProduct), [
                'product'    => $wishlistProduct->getProduct(),
                'data'       => $wishlistProduct->getVariant(),
                'data_class' => null,
                'label'      => false,
            ])
            ->add('quantity', IntegerType::class, [
                'mapped' => true,
                'required' => false,
                'empty_data' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('wishlistProduct')
            ->setAllowedTypes('wishlistProduct', [WishlistProductInterface::class, 'null'])
            ->setDefault('data-class', WishlistProduct::class)
        ;
    }

    private function getVariantType(WishlistProductInterface $wishlistProduct)
    {
        return $wishlistProduct->getProduct()->getVariantSelectionMethod() === ProductInterface::VARIANT_SELECTION_CHOICE ?
            ProductVariantChoiceType::class :
            ProductVariantMatchType::class
            ;
    }
}
