<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('wishlist_file', FileType::class, [
                'label' => 'Wishlist (CSV file)',
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
                'label' => 'Submit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'maxFileSize' => '512k',
            'allowedMimeTypes' => [
                'text/plain',
                'text/csv',
                'application/csv'
            ],
            'wishlists' => [],
        ]);

        $resolver->setAllowedTypes('maxFileSize', 'string');
        $resolver->setAllowedTypes('allowedMimeTypes', 'array');
        $resolver->setAllowedTypes('wishlists', 'array');
    }
}
