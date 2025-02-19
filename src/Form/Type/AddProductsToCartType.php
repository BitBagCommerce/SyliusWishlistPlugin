<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use BitBag\SyliusWishlistPlugin\Entity\WishlistProductInterface;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddProductsToCartType extends AbstractType
{
    public function __construct(
        private AddToCartCommandFactoryInterface $addToCartCommandFactory,
        private CartItemFactoryInterface $cartItemFactory,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options): void {
            $form = $event->getForm();

            $wishlistProduct = $form->getData()->getWishlistProduct();

            $form
                ->add('cartItem', AddToCartType::class, [
                'label' => false,
                'required' => false,
                'product' => $wishlistProduct->getProduct(),
                'data' => $this->addToCartCommandFactory->createWithCartAndCartItem(
                    $options['cart'],
                    $this->createCartItem($wishlistProduct),
                ),
                'is_wishlist' => true,
                ])
                ->add('selected', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                ]);
        });
    }

    private function createCartItem(WishlistProductInterface $wishlistProduct): OrderItemInterface
    {
        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createForProduct($wishlistProduct->getProduct());
        $cartItem->setVariant($wishlistProduct->getVariant());

        $this->orderItemQuantityModifier->modify($cartItem, 0);

        return $cartItem;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('cart')
            ->setDefault('data_class', WishlistItem::class)
            ->setDefault('allow_extra_fields', true);
    }
}
