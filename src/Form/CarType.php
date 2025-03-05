<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('brand', TextType::class, [
            'label' => 'Brand',
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'message' => 'The brand can only contain letters.',
                ]),
            ],
        ])
        ->add('model', TextType::class, [
            'label' => 'Model',
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z0-9\s]+$/',
                    'message' => 'The model can only contain letters and numbers.',
                ]),
            ],
        ])
            ->add('registration', TextType::class, [
                'label' => 'Registration',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^([A-Z]{2} \d{3} [A-Z]{2}|\d{1,4} [A-Z]{2} \d{2,3})$/',
                        'message' => 'Enter a valid French license plate (ex: AB 123 CD or 123 AB 75).',
                    ]),
                ],
            ])
            ->add('nbSeat', IntegerType::class, [
                'label' => 'Number of Seats',
                'attr' => ['min' => 1, 'max' => 20],
            ])
            ->add('bootCapacity', IntegerType::class, [
                'label' => 'Boot Capacity (in liters)',
                'attr' => ['min' => 0],
            ])
            ->add('fuelType', ChoiceType::class, [
                'label' => 'Fuel Type',
                'choices' => [
                    'Petrol' => 'petrol',
                    'Diesel' => 'diesel',
                    'Electric' => 'electric',
                    'Hybrid' => 'hybrid',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
