<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use BitBag\SyliusWishlistPlugin\Processor\SelectedWishlistProductsProcessorInterface;
use Sylius\Bundle\OrderBundle\Form\Type\CartType as BaseCartType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;
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
            ->add('items', LiveCollectionType::class, [
                'entry_type' => AddProductsToCartType::class,
                'entry_options' => [
                    'cart' => $options['cart'],
                ],
                'allow_add' => false,
                'allow_delete' => false,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('addAll', SubmitType::class, [
                'label' => 'bitbag_sylius_wishlist_plugin.ui.add_items_to_cart',
                'attr' => [
                    'class' => 'ui primary button',
                ],
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

    public function getBlockPrefix(): string
    {
        return 'wishlist_cart';
    }

    public function getParent(): string
    {
        return BaseCartType::class;
    }
}
