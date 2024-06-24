<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use BitBag\SyliusWishlistPlugin\Entity\Wishlist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

final class ImportWishlistFromCsvType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wishlist_file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => $options['maxFileSize'],
                        'mimeTypes' => $options['allowedMimeTypes'],
                        'mimeTypesMessage' => 'Please upload a valid CSV file',
                    ]),
                ],
            ])
            ->add('wishlists', EntityType::class, [
                'class' => Wishlist::class,
                'choices' => $options['wishlists'],
                'choice_label' => 'name',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'bitbag_sylius_wishlist_plugin.ui.submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'maxFileSize' => '512k',
            'allowedMimeTypes' => [
                'text/plain',
                'text/csv',
                'application/csv',
            ],
            'wishlists' => [],
        ]);

        $resolver->setAllowedTypes('maxFileSize', 'string');
        $resolver->setAllowedTypes('allowedMimeTypes', 'array');
        $resolver->setAllowedTypes('wishlists', 'array');
    }
}
