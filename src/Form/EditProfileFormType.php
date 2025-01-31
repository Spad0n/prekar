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

class EditProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'];
        $currentRoles = $user->getRoles();
        $builder
            ->add('email', EmailType::class)
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
