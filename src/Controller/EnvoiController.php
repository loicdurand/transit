<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Controller\TransitController;
use App\Entity\User;
use App\Entity\Envoi;
use App\Entity\Action;
use App\Form\EnvoiCompletionType;

final class EnvoiController extends TransitController
{
    #[Route('/envoi', name: 'transit_envoi')]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $envoi_id = $this->request->query->get('envoi');
        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);
        $actions = $envoi->getActions(); //  les Actions sont toujours triées par rang (cf App\Entity\Envoi::getActions())
        $current_action = $actions[0];
        foreach ($actions as $i => $action) {
            if ($action->isResultat() == true) {
                $current_action = $actions[$i + 1];
            }
        }


        $envoi->setStatut($current_action->getEtape()->getStatutSiNegatif());

        $form = $this->createForm(EnvoiCompletionType::class, $envoi);

        $form->handleRequest($this->request);
        // if ($form->isSubmitted() && $form->isValid()) {
        // }

        return $this->render('envoi/index.html.twig', [
            'envoi' => $envoi,
            'form' => $form
        ]);
    }
}
