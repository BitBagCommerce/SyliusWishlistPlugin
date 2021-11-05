<?php
declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
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
                        'maxSize' => '512k',
                        'mimeTypes' => [
                            'text/plain',
                            'text/csv',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid CSV file',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit'
            ]);
    }
}

