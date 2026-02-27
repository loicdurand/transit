<?php

namespace App\Form;

use App\Entity\Destinataire;
use App\Entity\Envoi;
use App\Entity\Objet;
use App\Entity\StatutEnvoi;
use App\Entity\TypeEnvoi;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnvoiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            // ->add('reference')
            ->add('date')
            // ->add('quantite')
            ->add('destinataire', EntityType::class, [
                'class' => Destinataire::class,
                'choice_label' => 'libelle',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.id', 'DESC');
                },
            ])
            ->add('objet', EntityType::class, [
                'class' => Objet::class,
                'choice_label' => 'libelle',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('o')
                        ->orderBy('o.id', 'DESC');
                },

            ])
            // ->add('type', EntityType::class, [
            //     'class' => TypeEnvoi::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('statut', EntityType::class, [
            //     'class' => StatutEnvoi::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Envoi::class
        ]);
    }
}
