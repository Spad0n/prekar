<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Offer;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('car', EntityType::class, [
                'class' => Car::class,
                'choice_label' => function (Car $car) {
                    return $car->getBrand() . ' ' . $car->getModel();
                },
                'label' => 'Select Car',
            ])
            ->add('startDate', null, [
                'widget' => 'single_text',
                'label' => 'Start Date',])
            ->add('endDate', null, [
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
                'required' => false,
                'label' => 'Availability',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
