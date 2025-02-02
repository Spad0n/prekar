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

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, ['label' => 'Brand'])
            ->add('model', TextType::class, ['label' => 'Model'])
            ->add('registration', TextType::class, ['label' => 'Registration'])
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
