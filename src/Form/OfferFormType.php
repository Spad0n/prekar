<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Offer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Regex;

class OfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['is_edit']) {
            $builder
                ->add('startDate', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Start Date',
                ])
                ->add('endDate', DateType::class, [
                    'widget' => 'single_text',
                    'required' => false,
                    'label' => 'End Date (Optional)',
                ])
                ->add('localisationGarage', TextType::class, [
                    'required' => false,
                    'label' => 'Garage Location',
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^\d+\s+[\p{L}\p{N}\s\'-]+,\s+[\p{L}\p{N}\s\'-]+$/u',
                            'message' => 'The address is not valid. Example: 1 Bd des Aiguillettes, Vandœuvre-lès-Nancy',
                        ]),
                    ],
                ])
                ->add('price', MoneyType::class, [
                    'currency' => 'EUR',
                    'label' => 'Price',
                    'attr' => ['min' => 100],
                ])
                ->add('delivery', ChoiceType::class, [
                    'choices' => [
                        'Yes' => 'yes',
                        'No' => 'no',
                    ],
                    'required' => false,
                    'label' => 'Delivery Option',
                ])
                ->add('available', ChoiceType::class, [
                    'choices' => [
                        'Available' => 'available',
                        'Not Available' => 'not_available',
                    ],
                    'label' => 'Availability',
                ])
                ->add('car', CarType::class, [
                    'data' => $options['car'],
                    'mapped' => false,
                ]);
        } else {
            $builder
                ->add('existingCar', EntityType::class, [
                    'class' => Car::class,
                    'choices' => $options['user_cars'],
                    'choice_label' => function (Car $car) {
                        return $car->getBrand() . ' ' . $car->getModel() . ' (' . $car->getRegistration() . ')';
                    },
                    'required' => false,
                    'placeholder' => 'Choose an existing car',
                    'mapped' => false,
                    'attr' => ['class' => 'existingCar'],
                ])
                ->add('newCar', CarType::class, [
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['class' => 'newCar'],
                ])
                ->add('startDate', DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Start Date',
                ])
                ->add('endDate', DateType::class, [
                    'widget' => 'single_text',
                    'required' => false,
                    'label' => 'End Date (Optional)',
                ])
                ->add('localisationGarage', TextType::class, [
                    'required' => false,
                    'label' => 'Garage Location',
                ])
                ->add('price', MoneyType::class, [
                    'currency' => 'EUR',
                    'label' => 'Price',
                    'attr' => ['min' => 99],
                    'constraints' => [
                        new Assert\GreaterThan([
                            'value' => 99,
                            'message' => 'The price must be greater than 99.',
                        ]),
                    ],
                ])
                ->add('delivery', ChoiceType::class, [
                    'choices' => [
                        'Yes' => 'yes',
                        'No' => 'no',
                    ],
                    'required' => false,
                    'label' => 'Delivery Option',
                ])
                ->add('available', ChoiceType::class, [
                    'choices' => [
                        'Available' => 'available',
                        'Not Available' => 'not_available',
                    ],
                    'label' => 'Availability',
                    'mapped' => true,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
            'user_cars' => [],
            'csrf_protection' => false, // for test
            'car' => null,
            'is_edit' => false,
        ]);
    }
}