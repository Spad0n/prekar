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
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options, ): void
    {
        $user = $options['data'];
        $currentRoles = $user->getRoles();
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
                ]
            )
            ->add('lastName', TextType::class)
            ->add('name', TextType::class)
            ->add('userType', ChoiceType::class, [
                'choices' => [
                    'Emprunteur' => 'ROLE_BORROWER',
                    'Propriétaire' => 'ROLE_OWNER',
                ],
                'label' => 'Account Type',
                'multiple' => true,
                'expanded' => true,
                'mapped' => true,
                'disabled' => true,
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
