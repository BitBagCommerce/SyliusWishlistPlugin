<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProductsToCartType extends AbstractType
{
    /** @var AddToCartCommandFactoryInterface */
    private $addToCartCommandFactory;

    /** @var FactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    /** @var string[] */
    private $validationGroups;

    public function __construct(
        AddToCartCommandFactoryInterface $addToCartCommandFactory,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        array $validationGroups
    ) {
        $this->addToCartCommandFactory = $addToCartCommandFactory;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
        $this->validationGroups = $validationGroups;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($options['products'] as $key => $product) {
            $builder
                ->add($key, AddToCartType::class, [
                    'label' => false,
                    'required' => false,
                    'product' => $product,
                    'data' => $this->addToCartCommandFactory->createWithCartAndCartItem(
                        $options['cart'],
                        $this->createCartItem($product)
                    ),
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('cart')
            ->setRequired('products')
            ->setAllowedTypes('products', Collection::class)
            ->setDefault('data_class', null)
            ->setDefault('validation_groups', $this->validationGroups)
        ;
    }

    private function createCartItem(ProductInterface $product): OrderItemInterface
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForProduct($product);

        $this->orderItemQuantityModifier->modify($cartItem, 0);

        return $cartItem;
    }
}
