<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Controller\TransitController;
use App\Entity\User;
use App\Entity\Envoi;
use App\Entity\StatutEnvoi;
use App\Form\EnvoiType;

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

        $statut_initial = $entityManager->getRepository(StatutEnvoi::class)->findOneBy(['libelle' => 'Initial']);
        $envoi = new Envoi();
        $envoi->setDate(new \Datetime('now'));
        $envoi->setStatut($statut_initial);

        $form = $this->createForm(EnvoiType::class, $envoi);

        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($envoi);
            $entityManager->flush();

            return $this->redirectToRoute('transit_index', []);
        }

        return $this->render('index/creer-envoi.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }
}
