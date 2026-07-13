<?php

namespace App\Form\Type;

use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class AlbumType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            -> add('albumTitle', TextType::class)
            -> add('albumCover', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File(
                        mimeTypes: [
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        ],
                        mimeTypesMessage: 'Upload only image (JPG, PNG of WEBP).',
                        maxSize: '5M'
                    ),
                ],
                'attr' => [
                    'accept' => 'image/*'
                ]
            ])
            -> add('info', TextType::class, [
                'required' => false,
            ])
            -> add('releaseDate', DateType::class)
            -> add('save', SubmitType::class);
    }

}