<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_.+-]+@?(deloitte).com/',
                        'message' => 'Le nom de l\'adresse email doit contenir @deloitte.com'
                    ]),
                    new NotBlank([
                        'message' => 'Email obligatoire',
                    ]),
                ]
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de pass obligatoire',
                    ]),
                    new Length([
                        // Le mot de pass à au moins 8 caractères
                        'min' => 8,
                        'minMessage' => 'Ton mot de pass doit être de {{ limit }} caractères minimum',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        // au moins une lettre et un chiffre
                        'pattern' => '/^(?=.*[a-z])(?=.*[0-9])(?=.{8,})/',
                        'message' => 'Ton mot de pass doit posséder au moins une lettre et un nombre',
                    ])
                ],
            ])
            ->add('nom')
            ->add('prenom')
            ->add('secteur', ChoiceType::class, [
                'choices' => [
                    'Direction' => 'Direction',
                    'Informatique' => 'Informatique',
                    'Recrutement' => 'Recrutement',
                    'Comptabilité' => 'Comptabilité',
                ],
            ])
            // Pas obligatoire mais on peut attribuer des rôles comme cela

            // ->add('roles', ChoiceType::class, [
            //     'choices' => [
            //         'Salarié' => 'ROLE_SALARIE',
            //         'Secretaire' => 'ROLE_ADMIN'
            //     ],
            //     'multiple' => true,
            //     'expanded' => true
            // ])

            ->add('photo', FileType::class, [
                'label' => 'Image (JPG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Photo obligatoire',
                    ]),
                    new File([
                        'maxSize' => '10000024k',
                        'mimeTypes' => [
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPG',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
