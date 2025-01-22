<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchChampionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameOne')
            ->add('hasteOne')
            ->add('nameTwo')
            ->add('hasteTwo')
            ->add('nameThree')
            ->add('hasteThree')
            ->add('nameFour')
            ->add('hasteFour')
            ->add('nameFive')
            ->add('hasteFive')
            ->add('nameSix')
            ->add('hasteSix')
            ->add('nameSeven')
            ->add('hasteSeven')
            ->add('nameEight')
            ->add('hasteEight')
            ->add('nameNine')
            ->add('hasteNine')
            ->add('nameTen')
            ->add('hasteTen')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchChampionDTO::class,
        ]);
    }
}
