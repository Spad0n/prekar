<?php

namespace App\Form;

use App\Entity\Dispute;
use App\Entity\Jurist;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;

class DisputeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $renting = $options['renting'];
        $today = new DateTime();
        $today->setTime(0, 0);
        $builder
            ->add('description')
            ->add('reportingDate', null, [
                'widget' => 'single_text',
                'html5'=> true,
                'attr'=>[
                    'max' => (new \DateTime())->format('Y-m-d')
                ],
                'constraints'=>[
                    new Range([
                        'min' => $renting->getStartDate()->format('Y-m-d'),
                        'max' => $renting->getEndDate()->format('Y-m-d'),
                    ]),
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dispute::class,
            'renting' =>null,
        ]);
    }
}
