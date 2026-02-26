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
use App\Entity\TypeEnvoi;
use App\Entity\Numero;
use App\Form\EnvoiCompletionType;
use App\Form\NumeroType;


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

        $numero = new Numero();
        $numero->setEnvoi($envoi);
        $numero_form = $this->createForm(NumeroType::class, $numero);

        return $this->render('envoi/index.html.twig', [
            'user' => $user,
            'envoi' => $envoi,
            'form' => $form,
            'numero_form' => $numero_form
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

        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);
        $current_action = $action_suivante ? $action_suivante : null;
        if ($current_action != null) {
            $envoi->setStatut($current_action->getEtape()->getStatutSiNegatif());
        } else {
            $statut_final = $entityManager->getRepository(StatutEnvoi::class)->findOneBy(['libelle' => $this->statut_final_libelle]);
            $envoi->setStatut($statut_final);
        }
        $entityManager->persist($envoi);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => $action,
            'statut_suivant' => $action_suivante ? $action_suivante->getEtape()->getStatutSiNegatif() : null
        ]);
    }


    #[Route('/envoi/sauver-donnee', name: 'transit_envoi_sauverdonnee', methods: ['POST'])]
    public function sauverdonnee(EntityManagerInterface $entityManager)
    {
        $data = (array) json_decode($this->request->getContent());

        $envoi_id = $data['envoi_id'];
        $field = $data['field'];
        $value = $data['value'];

        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);

        switch ($field) {
            case 'type':
                $type = $entityManager->getRepository(TypeEnvoi::class)->find($value);
                $envoi->setType($type);
                break;

            default:
                $method = 'set' . ucfirst($field);
                $value = $value != '' ? $value : null;
                $envoi->$method($value);
                break;
        }

        $entityManager->persist($envoi);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => $envoi
        ]);
    }

    #[Route('/envoi/sauver-numero', name: 'transit_envoi_sauvernumero', methods: ['POST'])]
    public function sauvernumero(EntityManagerInterface $entityManager)
    {
        $data = (array) json_decode($this->request->getContent());

        $envoi_id = $data['envoi_id'];
        $numero_id = $data['numero_id'];
        $libelle = $data['libelle'];
        $valeur = $data['valeur'];

        $envoi = $entityManager->getRepository(Envoi::class)->find($envoi_id);
        $numero_exists = $numero_id !== '' ? $entityManager->getRepository(Numero::class)->find($numero_id) : null;
        $numero = !is_null($numero_exists) ? $numero_exists : new Numero();
        $numero->setEnvoi($envoi);
        $numero->setLibelle($libelle);
        $numero->setValeur($valeur);
        $envoi->addNumero($numero);
        $entityManager->persist($envoi);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => $numero
        ]);
    }

    #[Route('/envoi/supprimer-numero/{numero_id}', name: 'transit_envoi_supprimernumero', methods: ['DELETE'])]
    public function supprimer(EntityManagerInterface $entityManager, string $numero_id)
    {
        $numero = $entityManager->getRepository(Numero::class)->find($numero_id);
        $entityManager->remove($numero);
        $entityManager->flush();

        return $this->json([
            'success' => true
        ]);
    }
}
