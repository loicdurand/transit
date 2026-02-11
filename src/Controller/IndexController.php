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
                    'id' => $objet->getId()
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

        $id = $this->request->query->get('id');
        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $objet = $entityManager->getRepository(Objet::class)->find($id);

        $statuts = $entityManager->getRepository(StatutEnvoi::class)->findAll();
        $statuts = array_filter($statuts, function ($statut) {
            return $statut->getLibelle() !== $this->statut_initial_libelle;
        });

        return $this->render('index/init-objet.html.twig', [
            'objet' => $objet,
            'statuts' => $statuts
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
}
