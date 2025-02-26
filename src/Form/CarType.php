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
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, ['label' => 'Brand'])
            ->add('model', TextType::class, ['label' => 'Model'])
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
            ->add('image', FileType::class, [
                'label' => 'Car Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG or PNG)',
                    ])
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
