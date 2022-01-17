<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use BitBag\SyliusWishlistPlugin\Command\Wishlist\WishlistItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WishlistCollectionType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => AddProductsToCartType::class,
                'entry_options' => [
                    'cart' => $options['cart'],
                ],
            ])
            ->add('addAll', SubmitType::class, [
                'label' => 'bitbag_sylius_wishlist_plugin.ui.add_items_to_cart',
                'attr' => [
                    'class' => 'ui primary button',
                ],
            ])
            ->addEventListener(
                FormEvents::SUBMIT,
                [$this, 'pickSelectedWishlistItems']
            )
        ;
    }

    public function pickSelectedWishlistItems(FormEvent $event): void
    {
        if ($event->getForm()->get('addAll')->isClicked()) {
            return;
        }

        $selectedProducts = $this->createSelectedWishlistProductsCollection($event);

        if ($selectedProducts->isEmpty()) {
            $event->getForm()->addError(new FormError($this->translator->trans('bitbag_sylius_wishlist_plugin.ui.select_products')));
        }

        $event->setData($selectedProducts);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('cart')
            ->setDefault('data_class', null);
    }

    private function createSelectedWishlistProductsCollection(FormEvent $event): Collection
    {
        $selectedProducts = new ArrayCollection();

        foreach ($event->getData() as $wishlistProducts) {
            /** @var WishlistItem $wishlistProduct */
            foreach ($wishlistProducts as $wishlistProduct) {
                if (true === $wishlistProduct->isSelected()) {
                    $selectedProducts->add($wishlistProduct);
                }
            }
        }

        return $selectedProducts;
    }
}
