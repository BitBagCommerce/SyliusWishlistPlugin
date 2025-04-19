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

namespace Sylius\WishlistPlugin\Form\Type;

use Sylius\WishlistPlugin\Processor\SelectedWishlistProductsProcessorInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

final class WishlistCollectionType extends AbstractType
{
    public function __construct(
        private TranslatorInterface $translator,
        private SelectedWishlistProductsProcessorInterface $selectedWishlistProductsProcessor,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            ])
            ->addEventListener(
                FormEvents::SUBMIT,
                [$this, 'pickSelectedWishlistItems'],
            )
        ;
    }

    public function pickSelectedWishlistItems(FormEvent $event): void
    {
        /** @var FormInterface $submitButton */
        $submitButton = $event->getForm()->get('addAll');
        Assert::isInstanceOf($submitButton, SubmitButton::class);

        if ($submitButton->isClicked()) {
            return;
        }

        $selectedProducts = $this->
        selectedWishlistProductsProcessor->
        createSelectedWishlistProductsCollection(
            $event->getForm()->get('items')->getData(),
        );

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
}
