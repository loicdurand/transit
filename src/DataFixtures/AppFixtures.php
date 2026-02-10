<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\TypeEnvoi;
use App\Entity\StatutEnvoi;
use App\Entity\Destinataire;
use App\Entity\Objet;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // INSERTION DES TYPES D'ENVOIS (COLIS, PALETTE)

        $types_envoi = [
            [
                'libelle' => 'palette',
                'max' => null
            ],
            [
                'libelle' => 'colis',
                'max' => 5
            ]
        ];

        foreach ($types_envoi as $type_envoi) {
            $entity = new TypeEnvoi();
            $entity->setLibelle($type_envoi['libelle']);
            $entity->setMaximum($type_envoi['max']);
            $manager->persist($entity);
            $manager->flush();
        }

        // INSERTION DES STAUTS D'ENVOI, POUR SUIVRE LES ÉTAPES

        $statuts = [
            'Initial',
            'Doc à finaliser',
            'Envoi à créer sur SCRTASIC',
            'Doc à envoyer au SCRTA',
            'Envoyer liste de chargement à STT',
            'En attente réception FR302',
            'Renseigner n° FR302 sur liste de chargement',
            'Envoyer le FR302 + LC au SCRTA',
            'Finalisé'
        ];

        foreach ($statuts as $libelle) {
            $entity = new StatutEnvoi();
            $entity->setLibelle($libelle);
            $manager->persist($entity);
            $manager->flush();
        }

        // INSERTION DES DESTINATAIRES (SCRTA, SXM, ETC...)

        $destinataires = [
            'Autre',
            'SCRTA',
            'SAINT-MARTIN'
        ];

        foreach ($destinataires as $destinataire) {
            $entity = new Destinataire();
            $entity->setLibelle($destinataire);
            $manager->persist($entity);
            $manager->flush();
        }

        // INSERTION DES OBJETS (Envoi MCO, Barge SXM, ETC...)

        $objets = [
            'Autre',
            'Envoi MCO',
            'Barge SXM'
        ];

        foreach ($objets as $objet) {
            $entity = new Objet();
            $entity->setLibelle($objet);
            $manager->persist($entity);
            $manager->flush();
        }

        $manager->flush();
    }
}
