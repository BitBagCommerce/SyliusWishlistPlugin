<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class WishlistProductCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $data = $event->getData();

                    foreach ($form as $key => $data) {
                        $form->remove($key);
                    }

                    foreach ($data as $key => $wishlistProduct) {
                        $form->add($key, WishlistProductType::class, [
                            'wishlistProduct' => $wishlistProduct,
                            'data'            => $wishlistProduct,
                            'property_path'   => '['.$key.']',

                            'constraints' => [
                                new UniqueEntity([
                                    'fields'  => ['variant', 'wishlist'],
                                    'message' => 'bitbag_sylius_wishlist_plugin.ui.variant_not_unique',
                                ]),
                            ],
                            'error_bubbling' => false,
                        ]);
                    }
                }
            )
        ;
    }
}
