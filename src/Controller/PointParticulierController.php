<?php

namespace App\Controller;

use App\Entity\PointParticulier;
use App\Form\PointParticulierType;
use App\Repository\PointParticulierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/point/particulier')]
final class PointParticulierController extends AbstractController
{
    #[Route(name: 'transit_point_particulier_index', methods: ['GET'])]
    public function index(PointParticulierRepository $pointParticulierRepository): Response
    {
        return $this->render('point_particulier/index.html.twig', [
            'point_particuliers' => $pointParticulierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'transit_point_particulier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pointParticulier = new PointParticulier();
        $form = $this->createForm(PointParticulierType::class, $pointParticulier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pointParticulier);
            $entityManager->flush();

            return $this->redirectToRoute('transit_point_particulier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('point_particulier/new.html.twig', [
            'point_particulier' => $pointParticulier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'transit_point_particulier_show', methods: ['GET'])]
    public function show(PointParticulier $pointParticulier): Response
    {
        return $this->render('point_particulier/show.html.twig', [
            'point_particulier' => $pointParticulier,
        ]);
    }

    #[Route('/{id}/edit', name: 'transit_point_particulier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PointParticulier $pointParticulier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PointParticulierType::class, $pointParticulier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pointParticulier = $form->getData();
            $entityManager->flush();

            return $this->redirectToRoute('transit_envoi', [
                'envoi' => $pointParticulier->getEnvoi()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('point_particulier/edit.html.twig', [
            'point_particulier' => $pointParticulier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'transit_point_particulier_delete', methods: ['POST'])]
    public function delete(Request $request, PointParticulier $pointParticulier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pointParticulier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pointParticulier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transit_envoi', [
            'envoi' => $pointParticulier->getEnvoi()->getId()
        ], Response::HTTP_SEE_OTHER);
    }
}
