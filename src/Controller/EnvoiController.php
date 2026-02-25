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
use App\Entity\StatutEnvoi;
use App\Form\EnvoiCompletionType;

final class EnvoiController extends TransitController
{
    #[Route('/envoi', name: 'transit_envoi')]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager): Response
    {

        if (is_null($user))
            return $this->redirectToRoute('transit_login');

        $statut_final = $entityManager->getRepository(StatutEnvoi::class)->findOneBy(['libelle' => $this->statut_final_libelle]);

        $envoi_id = $this->request->query->get('envoi');
        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);
        $actions = $envoi->getActions(); //  les Actions sont toujours triées par rang (cf App\Entity\Envoi::getActions())
        $current_action = $actions[0];
        foreach ($actions as $i => $action) {
            if ($action->isResultat() == true) {
                $current_action = $actions[$i + 1];
            }
        }

        if ($current_action != null)
            $envoi->setStatut($current_action->getEtape()->getStatutSiNegatif());
        else
            $envoi->setStatut($statut_final);

        $form = $this->createForm(EnvoiCompletionType::class, $envoi);

        $form->handleRequest($this->request);
        // if ($form->isSubmitted() && $form->isValid()) {
        // }

        return $this->render('envoi/index.html.twig', [
            'user' => $user,
            'envoi' => $envoi,
            'form' => $form
        ]);
    }

    #[Route('/envoi/marquer-action-traitee', name: 'transit_envoi_marqueractiontraitee', methods: ['POST'])]
    public function marqueractiontraitee(EntityManagerInterface $entityManager)
    {
        $data = (array) json_decode($this->request->getContent());

        $envoi_id = $data['envoi_id'];
        $action_id = $data['action_id'];
        $checked = $data['checked'];

        $action = $entityManager->getRepository(Action::class)->find($action_id);
        $action->setResultat($checked);
        $entityManager->persist($action);
        $entityManager->flush();

        $action_suivante = $entityManager->getRepository(Action::class)->findOneBy([
            'envoi' => $envoi_id,
            'rang' => $checked ? $action->getRang() + 1 : $action->getRang()
        ]);

        return $this->json([
            'success' => true,
            'data' => $action,
            'statut_suivant' => $action_suivante ? $action_suivante->getEtape()->getStatutSiNegatif() : null
        ]);
    }
}
