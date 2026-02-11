<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Controller\TransitController;
use App\Entity\User;
use App\Entity\Envoi;

final class EnvoiController extends TransitController
{
    #[Route('/envoi/{id}', name: 'transit_envoi')]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager, string $id): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $envoi = $entityManager->getRepository(Envoi::class)->find($id);
        dd($envoi);

        return $this->render('envoi/index.html.twig', []);
    }
}
