<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\TypeEnvoi;
use App\Entity\StatutEnvoi;
use App\Entity\Etape;
use App\Entity\Action;
use App\Entity\Destinataire;
use App\Entity\Objet;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // INSERTION DES TYPES D'ENVOIS (COLIS, PALETTE)

        $types_envoi = [
            [
                'libelle' => 'Palette',
                'max' => null
            ],
            [
                'libelle' => 'Colis',
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

        $envoi_MCO = new Objet();

        $objets = [
            'Autre',
            'Envoi MCO',
            'Barge SXM'
        ];

        foreach ($objets as $objet_libelle) {
            $entity = new Objet();
            $entity->setLibelle($objet_libelle);
            $manager->persist($entity);
            $manager->flush();
            if ($objet_libelle === 'Envoi MCO') {
                $envoi_MCO = $entity;
            }
        }

        // INSERTION DES STATUTS, ÉTAPES ET ACTIONS

        // Création d'un statut "Initial" 
        $statut = new StatutEnvoi();
        $statut->setLibelle('Initial');
        $manager->persist($statut);
        $manager->flush();

        $etapes_pour_ENVOI_MCO = [
            [
                'Avez-vous saisi les documents?',
                'Doc à finaliser'
            ],
            [
                'Avez-vous créé un nouvel envoi sur SCRTASIC?',
                'Envoi à créer sur SCRTASIC'
            ],
            [
                'Renseigner n° fiche de transport',
                'N° fiche de transport à renseigner'
            ],
            [
                'La liste de chargement a-t\'elle été envoyée au SCRTA pour validation?',
                'Doc à envoyer au SCRTA'
            ],
            [
                'La liste de chargement a-t\'elle été envoyé à la STT de Guadeloupe - pour création du FR302?',
                'Envoyer liste de chargement à STT'
            ],
            [
                'L\'originial du FR302 a-t\'il été récupéré à la SOLC?',
                'En attente réception FR302'
            ],
            [
                'Avez-vous renseigné le n° FR302 sur la liste de chargement?',
                'Renseigner n° FR302 sur liste de chargement'
            ],
            [
                'Avez-vous envoyé le FR302 ainsi que la liste de chargement complétée et signée au SCRTA?',
                'Envoyer le FR302 + LC au SCRTA'
            ],
            [
                'Avez-vous reçu le(s) étiquette(s) du SCRTA?',
                'En attente des étiquettes du SCRTA'
            ],
            [
                'L\'envoi a-t\'il été pris en compte par le transporteur?',
                'Filmer la palette / colis et appliquer les étiquettes + FR302 (dans pochette transparente non fermée)'
            ]
        ];

        $rang = 0;
        foreach ($etapes_pour_ENVOI_MCO as $etape) {
            [$etape_libelle, $statut_libelle] = $etape;
            // Statut
            $statut = new StatutEnvoi();
            $statut->setLibelle($statut_libelle);
            $manager->persist($statut);
            // Etape
            $etape = new Etape();
            $etape->setLibelle($etape_libelle);
            $etape->setStatutSiNegatif($statut);
            // Action
            $action = new Action();
            $action->setRang($rang);
            $action->setEtape($etape);
            $action->setObjet($envoi_MCO);

            $manager->persist($etape);
            $manager->persist($action);
            $manager->flush();
            $rang++;
        }

        $manager->flush();
    }
}
