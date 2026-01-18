<?php

namespace App\Form;

use App\Entity\TeacherApplication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeacherApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'label' => 'Full name',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('primaryTrack', ChoiceType::class, [
                'label' => 'Primary track',
                'choices' => [
                    'Linux & PrivEsc' => 'Linux & PrivEsc',
                    'Networking' => 'Networking',
                    'Web Security' => 'Web Security',
                    'OSINT / Recon' => 'OSINT / Recon',
                    'Blue Team / SOC' => 'Blue Team / SOC',
                ],
                'placeholder' => 'Choose a track',
            ])
            ->add('experienceLevel', ChoiceType::class, [
                'label' => 'Experience level',
                'choices' => [
                    '1–2 years' => '1–2 years',
                    '3–5 years' => '3–5 years',
                    '5+ years' => '5+ years',
                ],
                'placeholder' => 'Select',
            ])
            ->add('bio', TextareaType::class, [
                'label' => 'Short bio',
                'attr' => ['rows' => 5],
            ])
            ->add('links', TextType::class, [
                'label' => 'Links (optional)',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeacherApplication::class,
        ]);
    }
}
