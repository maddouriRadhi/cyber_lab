<?php

namespace App\Form;

use App\Entity\Lab;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LabType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, ['attr' => ['rows' => 6]])
            ->add('difficulty', ChoiceType::class, [
                'choices' => [
                    'Easy' => 'Easy',
                    'Medium' => 'Medium',
                    'Hard' => 'Hard',
                ]
            ])
            ->add('points', IntegerType::class)
            ->add('flag', TextType::class)
            ->add('scheduledAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Live session date/time (optional)',
            ])
            ->add('meetingLink', TextType::class, [
                'required' => false,
                'label' => 'Meeting link (optional)',
            ])
            ->add('maxSeats', IntegerType::class, [
                'required' => false,
                'label' => 'Max seats (optional)',
                'attr' => ['min' => 1],
            ])
            ->add('published', CheckboxType::class, [
                'required' => false,
                'label' => 'Published'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lab::class,
        ]);
    }
}
