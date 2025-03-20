<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'];
        $currentRoles = $user->getUserType();

        $choices = [
            'Borrower' => 'ROLE_BORROWER',
            'Owner' => 'ROLE_OWNER',
        ];
        $disabledChoices = array_fill_keys($currentRoles, true);
        $builder
            ->add('email', EmailType::class)
            ->add('lastName', TextType::class)
            ->add('name', TextType::class)
            ->add('userType', ChoiceType::class, [
                'choices' => $choices,
                'label' => 'Account Type',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'data' => $currentRoles,
                'choice_attr' => function ($choice, $key, $value) use ($disabledChoices) {
                    return isset($disabledChoices[$value]) ? ['disabled' => 'disabled'] : [];
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'You must select one or more roles.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'first_options' => ['label' => 'New password'],
                'second_options' => ['label' => 'Confirm password'],
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/[A-Z]/',
                        'message' => 'Your password must contain at least one uppercase letter',
                    ]),
                    new Regex([
                        'pattern' => '/[a-z]/',
                        'message' => 'Your password must contain at least one lowercase letter',
                    ]),
                    new Regex([
                        'pattern' => '/\d/',
                        'message' => 'Your password must contain at least one number',
                    ]),
                    new Regex([
                        'pattern' => '/[^a-zA-Z\d]/',
                        'message' => 'Your password must contain at least one special character',
                    ]),
                ],
            ])
            ->add('profileImage', FileType::class, [
                'label' => 'Profile picture',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Invalid file type. Please upload a JPG, PNG, or GIF.',
                    ])
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
