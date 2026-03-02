<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Envoi;
use App\Entity\PointParticulier;
use App\Entity\StatutPointParticulier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointParticulierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('envoi', EntityType::class, [
                'class' => Envoi::class,
                'choice_label' => 'titre',
                'disabled' => true,
                // 'attr' => [
                //     'class' => 'fr-hidden'
                // ],
                // 'label_attr' => [
                //     'class' => 'fr-hidden'
                // ]
            ])
            ->add('statut', EntityType::class, [
                'class' => StatutPointParticulier::class,
                'choice_label' => 'libelle',
            ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'MCA',
            ])
            ->add('quantite', null, [
                'attr' => [
                    'min' => 1
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PointParticulier::class,
        ]);
    }
}
