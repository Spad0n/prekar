<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Start Date',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'), // Set the minimum date to today
                ],
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'End Date',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'), // Set the minimum date to today
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
