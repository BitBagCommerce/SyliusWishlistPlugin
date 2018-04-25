<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProductsToCartType extends AbstractType
{
    /** @var AddToCartCommandFactoryInterface */
    private $addToCartCommandFactory;

    public function __construct(AddToCartCommandFactoryInterface $addToCartCommandFactory)
    {
        $this->addToCartCommandFactory = $addToCartCommandFactory;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['products'] as $key => $product) {
            $builder
                ->add($key, AddToCartType::class, [
                    'label' => false,
                    'required' => false,
                    'product' => $product,
                    'data' => $this->addToCartCommandFactory->createWithCartAndCartItem($options['cart'], $options['cartItem'])
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('cart')
            ->setAllowedTypes('cart', OrderInterface::class)
            ->setRequired('cartItem')
            ->setAllowedTypes('cartItem', OrderItemInterface::class)
            ->setRequired('products')
            ->setAllowedTypes('products', Collection::class)
            ->setDefault('data_class', null)
        ;
    }
}
