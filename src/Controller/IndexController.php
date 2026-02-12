<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Controller\TransitController;
use App\Entity\Destinataire;
use App\Entity\Objet;
use App\Entity\Action;
use App\Entity\User;
use App\Entity\Envoi;
use App\Entity\StatutEnvoi;
use App\Entity\Etape;
use App\Form\EnvoiType;
use App\Form\DestinataireType;
use App\Form\ObjetType;

final class IndexController extends TransitController
{

    #[Route('/', name: 'transit_index')]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $envois = $entityManager->getRepository(Envoi::class)->findAllUnfinalized();

        return $this->render('index/index.html.twig', [
            'user' => $user,
            'envois' => $envois
        ]);
    }

    #[Route('/creer-envoi', name: 'transit_index_creerenvoi')]
    public function creerenvoi(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $statut_initial = $entityManager->getRepository(StatutEnvoi::class)->findOneBy(['libelle' => $this->statut_initial_libelle]);
        $envoi = new Envoi();
        $envoi->setDate(new \Datetime('now'));
        $envoi->setStatut($statut_initial);

        $form = $this->createForm(EnvoiType::class, $envoi);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($envoi);
            $entityManager->flush();

            $objet = $envoi->getObjet();
            // On cherche dans la table Action si une action existe déjà pour cet objet.
            // Celà signifierai que l'Objet a déjà été configuré, et que l'on peut s'en servir de modèle pour cet envoi.
            $exists = $entityManager->getRepository(Action::class)->findOneBy(['objet' => $objet]);
            if (is_null($exists)) {
                return $this->redirectToRoute('transit_index_initobjet', [
                    'envoi' => $envoi->getId(),
                    'objet' => $objet->getId()
                ]);
            }

            return $this->redirectToRoute('transit_index', []);
        }

        return $this->render('index/creer-envoi.html.twig', [
            'user' => $user,
            'form' => $form,
            'destinataire_form' => $this->createForm(DestinataireType::class),
            'objet_form' => $this->createForm(ObjetType::class)
        ]);
    }

    #[Route('/init-objet', name: 'transit_index_initobjet')]
    public function initobjet(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $envoi_id = $this->request->query->get('envoi');
        $objet_id = $this->request->query->get('objet');

        $envoi = is_null($envoi_id) ? null : $entityManager->getRepository(Envoi::class)->find($envoi_id);
        $objet = is_null($objet_id) ? $envoi->getObjet() : $entityManager->getRepository(Objet::class)->find($objet_id);


        $statuts = $entityManager->getRepository(StatutEnvoi::class)->findAll();
        $statuts = array_filter($statuts, function ($statut) {
            return $statut->getLibelle() !== $this->statut_initial_libelle;
        });

        return $this->render('index/init-objet.html.twig', [
            'envoi' => $envoi,
            'objet' => $objet,
            'statuts' => $statuts
        ]);
    }

    #[Route('/recap-envoi', name: 'transit_index_recapenvoi')]
    public function recapenvoi(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $envoi_id = $this->request->query->get('envoi');

        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);

        if ($envoi->getActions()->count() === 0) {
            // on clône les actions de l'objet pour l'envoi
            $objet = $envoi->getObjet();
            $prev_actions = $entityManager->getRepository(Action::class)->findBy([
                'objet' => $objet->getId()
            ]);
            foreach ($prev_actions as $action) {
                $clone = new Action();
                $clone->setRang($action->getRang());
                $clone->setResultat(false);
                $clone->setEtape($action->getEtape());
                $clone->setEnvoi($envoi);
                $entityManager->persist($clone);
                $entityManager->flush();
                // On "recharge" l'envoi pour avoir les actions fraîchement insérées
                $envoi->addAction($clone);
            }
        }

        return $this->render('index/recap-envoi.html.twig', [
            'envoi' => $envoi
        ]);
    }

    #[Route('/sauver-destinataire', name: 'transit_index_sauverdestinataire', methods: ['POST'])]
    public function sauverdestinataire(EntityManagerInterface $entityManager)
    {
        $success = false;
        $data = (array) json_decode($this->request->getContent());
        $libelle = $data['libelle'];

        $exists = $entityManager->getRepository(Destinataire::class)->findOneBy(['libelle' => $libelle]);
        if (is_null($exists)) {
            $destinataire = new Destinataire();
            $destinataire->setLibelle($libelle);
            $entityManager->persist($destinataire);
            $entityManager->flush();
            $exists = $entityManager->getRepository(Destinataire::class)->findOneBy(['libelle' => $libelle]);
            $success = true;
        }

        return $this->json([
            'success' => $success,
            'data' => $exists
        ]);
    }

    #[Route('/sauver-objet', name: 'transit_index_sauverobjet', methods: ['POST'])]
    public function sauverobjet(EntityManagerInterface $entityManager)
    {
        $success = false;
        $data = (array) json_decode($this->request->getContent());
        $libelle = $data['libelle'];

        $exists = $entityManager->getRepository(Objet::class)->findOneBy(['libelle' => $libelle]);
        if (is_null($exists)) {
            $objet = new Objet();
            $objet->setLibelle($libelle);
            $entityManager->persist($objet);
            $entityManager->flush();
            $exists = $entityManager->getRepository(Objet::class)->findOneBy(['libelle' => $libelle]);
            $success = true;
        }

        return $this->json([
            'success' => $success,
            'data' => $exists
        ]);
    }

    #[Route('/sauver-actions', name: 'transit_index_sauveractions', methods: ['POST'])]
    public function sauveractions(EntityManagerInterface $entityManager)
    {
        $data = (array) json_decode($this->request->getContent());

        $envoi_id = $data['envoi_id'];
        $objet_id = $data['objet_id'];
        $etapes = $data['etapes'];

        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);

        $envoi_ou_objet = 'objet';

        // Si on a des actions déjà existantes pour cet objet, ça signifie que l'Objet a déjà été configuré.
        // On ne s'intéressera qu'à l'Envoi
        $prev_actions = $entityManager->getRepository(Action::class)->findBy([
            'objet' => $objet_id
        ]);

        if (count($prev_actions) > 0) {
            $envoi_ou_objet = 'envoi';
            $prev_actions = $entityManager->getRepository(Action::class)->findBy([
                'envoi' => $envoi_id
            ]);
        }

        foreach ($prev_actions as $action) {
            $entityManager->remove($action);
            $entityManager->flush();
            $envoi->removeAction($action);
        }

        foreach ($etapes as $etape) {
            $statut_libelle = $etape->statut;
            $statut_exists = $entityManager->getRepository(StatutEnvoi::class)->findOneBy(['libelle' => $statut_libelle]);
            if (is_null($statut_exists)) {
                $statut = new StatutEnvoi();
                $statut->setLibelle($statut_libelle);
                $entityManager->persist($statut);
                $entityManager->flush();
                $statut_exists = $entityManager->getRepository(StatutEnvoi::class)->findOneBy(['libelle' => $statut_libelle]);
            }
            $etape_libelle = $etape->libelle;
            $etape_exists = $entityManager->getRepository(Etape::class)->findOneBy(['libelle' => $etape_libelle]);
            if (is_null($etape_exists)) {
                $new_etape = new Etape();
                $new_etape->setLibelle($etape_libelle);
                $new_etape->setStatutSiNegatif($statut_exists);
                $entityManager->persist($new_etape);
                $entityManager->flush();
                $etape_exists = $entityManager->getRepository(Etape::class)->findOneBy([
                    'libelle' => $etape_libelle,
                    'statut_si_negatif' => $statut_exists->getId()
                ]);
            }
            $rang = $etape->rang;

            $action = new Action();
            $action->setRang($rang);
            $action->setResultat(false);
            $action->setEtape($etape_exists);
            if ($envoi_ou_objet === 'objet') {
                $objet = $entityManager->getRepository(Objet::class)->find($objet_id);
                $action->setObjet($objet);
            } else {
                $action->setEnvoi($envoi);
            }
            $entityManager->persist($action);
            $entityManager->flush();
        }

        $objet = $entityManager->getRepository(Objet::class)->find($objet_id);
        return $this->json([
            'success' => $objet->getActions()->count() > 0,
            'data' => $objet
        ]);
    }
}
