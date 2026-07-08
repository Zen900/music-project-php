<?php

namespace App\Form\Type;

use App\Entity\Song;
use App\Entity\Album;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('songTitle', TextType::class)
            ->add('audioFile', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File(
                        mimeTypes: [
                            'audio/mpeg',
                            'audio/wav',
                        ],
                        mimeTypesMessage: 'Upload only audio file.',
                        maxSize: '20M'
                    )
                ],
            ])
            -> add('info', TextType::class, [
                'required' => false,
            ])
            ->add('album', EntityType::class, [
                'class' => Album::class,
                'choice_label' => 'albumTitle',
            ])
            ->add('save', SubmitType::class);
    }
}