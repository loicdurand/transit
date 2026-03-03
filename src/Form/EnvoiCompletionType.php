<?php

namespace App\Form;

use App\Entity\Destinataire;
use App\Entity\Envoi;
use App\Entity\Objet;
use App\Entity\StatutEnvoi;
use App\Entity\TypeEnvoi;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class EnvoiCompletionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $envoi = $options['data'];
        $builder
            // ->add('titre')
            ->add('reference', null, [
                'label' => 'Référence principale', // $envoi->getDirection()->getLibelle() === 'envoi' ? 'Référence principale' : 'N° fiche de transport SCRTA',
                'attr' => [
                    'placeholder' => $envoi->getDirection()->getLibelle() === 'envoi' ? 'ex: Réforme N°X/20XX' : 'ex: 2601-01234'
                ]
            ])
            // ->add('date')
            ->add('type', EntityType::class, [
                'label' => 'Type d\'envoi',
                'class' => TypeEnvoi::class,
                'choice_label' => 'libelle',
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité',
                'html5' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 5
                ]
            ])
            // ->add('destinataire', EntityType::class, [
            //     'class' => Destinataire::class,
            //     'choice_label' => 'libelle',
            // ])
            // ->add('objet', EntityType::class, [
            //     'class' => Objet::class,
            //     'choice_label' => 'libelle',
            // ])

            // ->add('statut', EntityType::class, [
            //     'class' => StatutEnvoi::class,
            //     'choice_label' => 'libelle',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Envoi::class,
        ]);
    }
}
