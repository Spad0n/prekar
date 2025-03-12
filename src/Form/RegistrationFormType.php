<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter an email']),
                new Email(['message' => 'Please enter a valid email address'])
            ]
        ])
        ->add('password', PasswordType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Please enter a password']),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                ]),
                new Regex([
                    'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$/',
                    'message' => 'Your password must contain at least one uppercase letter, one lowercase letter, one number and one special character'
                ])
            ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre nom']),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre nom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre nom ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\\s\'-]+$/',
                        'message' => 'Votre nom ne peut contenir que des lettres, espaces, tirets et apostrophes'
                    ])
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre prénom']),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Votre prénom doit contenir au moins {{ limit }} caractères',
                        'maxMessage' => 'Votre prénom ne peut pas dépasser {{ limit }} caractères'
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-ZÀ-ÿ\\s\'-]+$/',
                        'message' => 'Votre prénom ne peut contenir que des lettres, espaces, tirets et apostrophes'
                    ])
                ]
            ])
            ->add('profileImage', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'L\'image doit être soit en JPG soit en PNG',
                    ])
                ],
            ])
            
            ->add('driverLicense', TextType::class, [
                'label' => 'Driver License Number',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your driver license number']),
                    new Length([
                        'min' => 9,
                        'max' => 20,
                        'minMessage' => 'Your driver license number must be at least {{ limit }} characters',
                        'maxMessage' => 'Your driver license number cannot be longer than {{ limit }} characters'
                    ]),
                    new Regex([
                        'pattern' => '/^[A-Z0-9]+$/',
                        'message' => 'Your driver license number can only contain uppercase letters and numbers'
                    ])
                ]
            ])

            ->add('userType', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Emprunteur' => 'ROLE_BORROWER',
                    'Propriétaire' => 'ROLE_OWNER',
                ],
                'label' => 'Type de compte',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un type de compte'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class  
        ]);
    }
}
