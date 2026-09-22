<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;



class ClientRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => "Nome",
                'label_attr' => [
                    'class' => 'input-label-absolute hidden-label',
                ],
                'attr' => [
                    'placeholder' => 'Nome',
                    'class' => 'lato-regular'
                ],
                'constraints' => [
                    new Assert\NotBlank(message:"Il nome è obbligatorio"),
                    new Assert\Length(max:30,min:1,maxMessage:"Il nome deve avere un massimo di 30 caratteri")
                ]
            ])

            ->add('day', DateType::class, [
                'label' => "Scegli il giorno",
                'attr' => [
                    'class' => "picker lato-regular",
                ],
                'label_attr' => [
                    'class' => 'input-label-absolute hidden-label'
                ],
                'constraints' => [
                    new Assert\NotBlank(message:"La data è obbligatoria"),
                ]
            ])

            ->add('email', EmailType::class, [
                'label' => "E-mail",
                'attr' => [
                    'placeholder' => 'E-mail',
                    'class' => 'lato-regular'
                ],
                'label_attr' => [
                    'class' => 'input-label-absolute hidden-label'
                ]
            ])

             ->add('time', TimeType::class, [
                'label' => "Scegli l'orario",
                'attr' => [
                    'class' => "time-picker lato-regular",
                    'data-time' => "Scegli l'orario"
                ],
                'label_attr' => [
                    'class' => 'input-label-absolute hidden-label'
                ]
            ])


            ->add("message",TextareaType::class,[
                'label' => "Scrivi un messaggio",
                'attr' => [
                    'placeholder' => "Scrivi un messaggio",
                    'class' => 'lato-regular'
                ],
                'label_attr' => [
                    'class' => 'input-label-absolute hidden-label'
                ]
            ])

            ->add("file",FileType::class,[
                'label' => "Allegato",
                'required' => false,
                'attr' => [
                    'placeholder' => "Allega un' immagine",
                    'class' => 'lato-regular'
                ],
                'label_attr' => [
                    'class' => 'input-label-absolute hidden-label'
                ]
            ])

            ->add("submit",SubmitType::class,[
                'label' => "PRENOTA APPUNTAMENTO",
                'attr' => [
                    'class' => "cta_btn cta_btn_active lato-black",
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
