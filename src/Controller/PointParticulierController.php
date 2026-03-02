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

use App\Entity\Envoi;

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

    #[Route('/new/{envoi}', name: 'transit_point_particulier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, string $envoi): Response
    {
        $pointParticulier = new PointParticulier();
        $form = $this->createForm(PointParticulierType::class, $pointParticulier);
        $form->handleRequest($request);
        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi);

        if ($form->isSubmitted() && $form->isValid()) {
            $pointParticulier->setEnvoi($envoi);
            $entityManager->persist($pointParticulier);
            $entityManager->flush();

            return $this->redirectToRoute('transit_envoi', [
                'envoi' => $envoi->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('point_particulier/new.html.twig', [
            'point_particulier' => $pointParticulier,
            'form' => $form,
            'envoi' => $envoi->getId()
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
