<?php

namespace App\Form;

use App\Entity\Envoi;
use App\Entity\Numero;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumeroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, [
                'label' => 'Type de référence (ex: Fiche de transport)'
            ])
            ->add('valeur', null, [
                'label' => 'Référence (ex: 01234/XXXX)',
                'help_attr' => [
                    'content' => 'Seulement la référence, sans indiquer "N°..."'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Numero::class,
        ]);
    }
}
