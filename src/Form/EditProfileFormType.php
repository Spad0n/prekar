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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

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
        $driverLicenseValue = $user->getDriverLicense();
        $isDriverLicenseEmpty = empty($driverLicenseValue);
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
                /*'constraints' => [
                    new NotBlank([
                        'message' => 'You must select one or more roles.',
                    ])
                ],*/
            ])
        
            ->add('driverLicense', TextType::class, [
                'label' => 'Driver License Number',
                'required' => false,
                'disabled' => !$isDriverLicenseEmpty, 
                'attr' => [
                    'readonly' => !$isDriverLicenseEmpty, 
                ],
                'help' => !$isDriverLicenseEmpty ? 'Driver license cannot be modified once set.' : '',
                'constraints' => $isDriverLicenseEmpty ? [
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
                ] : []
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
