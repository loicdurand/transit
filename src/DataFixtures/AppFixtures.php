<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\TypeEnvoi;
use App\Entity\StatutEnvoi;
use App\Entity\DirectionEnvoi;
use App\Entity\Etape;
use App\Entity\Action;
use App\Entity\Destinataire;
use App\Entity\Objet;
use App\Entity\StatutPointParticulier;
use App\Entity\Transport;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        // INSERTION DES TYPES D'ENVOIS (COLIS, PALETTE)

        $types_envoi = [
            [
                'libelle' => 'Sans objet',
                'max' => null
            ],
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

        // INSERTION DES DIRECTIONS (envoi, reception)
        $directions = [
            'envoi' => null,
            'reception' => null,
            'en instance' => null
        ];
        foreach (['envoi', 'reception', 'en instance'] as $libelle) {
            $direction = new DirectionEnvoi();
            $direction->setLibelle($libelle);
            $manager->persist($direction);
            $manager->flush();
            $directions[$libelle] = $direction;
        }

        // INSERTION DES TRANSPORTS (avion, voie maritime)
        $transports = [
            ['Non précisé', 'NP'],
            ['avion', 'VA'],
            ['Voie Maritime', 'VM']
        ];
        foreach ($transports as [$libelle, $abbr]) {
            $transport = new Transport();
            $transport->setLibelle($libelle);
            $transport->setAbbreviation($abbr);
            $manager->persist($transport);
            $manager->flush();
        }

        // INSERTION DES OBJETS (Envoi MCO, Barge SXM, ETC...)

        $envoi_MCO = new Objet();
        $reception_SCRTA = new Objet();
        $en_instance = new Objet();

        $objets = [
            ['Autre', null],
            ['Envoi MCO', $directions['envoi']],
            ['Barge SXM', $directions['envoi']],
            ['Réception SCRTA', $directions['reception']],
            ['Matériel en instance', $directions['en instance']]
        ];

        foreach ($objets as [$libelle, $direction]) {
            $entity = new Objet();
            $entity->setLibelle($libelle);
            $entity->setDirection($direction);
            $manager->persist($entity);
            $manager->flush();
            if ($libelle === 'Envoi MCO') {
                $envoi_MCO = $entity;
            } else if ($libelle === 'Réception SCRTA') {
                $reception_SCRTA = $entity;
            } else if ($libelle === 'Matériel en instance') {
                $en_instance = $entity;
            }
        }

        // INSERTION DES STATUTS, ÉTAPES ET ACTIONS

        // Création des statuts "Initial" et "Finalisé"
        foreach (['Initial', 'Finalisé'] as $libelle) {
            $statut = new StatutEnvoi();
            $statut->setLibelle($libelle);
            $manager->persist($statut);
            $manager->flush();
        }

        $actions = [
            // Étapes pour "Envoi MCO"
            $envoi_MCO->getId() => [
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
            ],
            // Étapes pour "Réception SCRTA"
            $reception_SCRTA->getId() => [
                [
                    'Matériel réceptionné?',
                    'En attente réception'
                ],
                [
                    'Pointage effectué?',
                    'Faire pointage'
                ],
                [
                    'Y a-t\'il des points particuliers?',
                    'Signaler les points particuliers'
                ],
                [
                    'SCRTASIC a-t\'il été validé et clôturé?',
                    'SCRTASIC à valider'
                ],
                [
                    'FR302 rempli et envoyé au SCRTA?',
                    'Remplir et envoyer FR302 au SCRTA'
                ],
                [
                    'Signature?',
                    'Signature'
                ]
            ],
            $en_instance->getId() => [
                [
                    'Matériel reçu?',
                    'En attente réception'
                ]
            ]
        ];

        foreach ($actions as $objet_id => $etapes) {
            $rang = 0;
            $objet = $manager->getRepository(Objet::class)->find($objet_id);
            foreach ($etapes as $etape) {
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
                $action->setObjet($objet);

                $manager->persist($etape);
                $manager->persist($action);
                $manager->flush();
                $rang++;
            }

            $manager->flush();
        }

        // INSERTION DES STATUTS DE POINTS PARTICULIERS

        foreach (['Article manquant', 'Article en instance', 'Cassé'] as $libelle) {
            $statut = new StatutPointParticulier();
            $statut->setLibelle($libelle);
            $manager->persist($statut);
            $manager->flush();
        }
    }
}
