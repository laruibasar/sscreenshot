<?php

namespace App\Form;

use App\Entity\Screenshot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScreenshotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', UrlType::class)
            ->add('width')
            ->add('height')
            ->add('output', ChoiceType::class, [
                'choices' => [
                    'image' => 'image',
                    'json' => 'json'
                ]
            ])
            ->add('file_type', ChoiceType::class, [
                'choices' => [
                    'png' => 'png',
                    'jpeg' => 'jpeg',
                    'webp' => 'webp',
                    'pdf' => 'pdf'
                ]
            ])
            ->add('lazy_load')
            ->add('dark_mode')
            ->add('grayscale', RangeType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 100
                ]
            ])
            ->add('delay')
            ->add('user_agent')
            ->add('full_page')
            ->add('fail_on_error')
            ->add('clip_x')
            ->add('clip_y')
            ->add('clip_w')
            ->add('clip_h')
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Screenshot::class,
        ]);
    }
}
