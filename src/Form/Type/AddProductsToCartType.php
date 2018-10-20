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

    /** @var CartItemFactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    public function __construct(
        AddToCartCommandFactoryInterface $addToCartCommandFactory,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->addToCartCommandFactory = $addToCartCommandFactory;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
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
