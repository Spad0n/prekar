<?php

namespace App\Form;

use App\Entity\Dispute;
use App\Entity\Jurist;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Range;

class DisputeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $renting = $options['renting'];

        $builder
            ->add('description')
            ->add('reportingDate', null, [
                'widget' => 'single_text',
                'constraints'=>[
                    new Range([
                        'min' => $renting->getStartDate()->format('Y-m-d'),
                        'max' => $renting->getEndDate()->format('Y-m-d'),
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dispute::class,
            'renting' => null,
        ]);
    }
}
