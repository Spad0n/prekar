<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Renting;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FinishRentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $renting = $options['renting'];
        $builder
            ->add('commentary',TextareaType::class,[
                'label'=>'Commentary',
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Renting::class,
            'renting' =>null,
        ]);
    }
}
