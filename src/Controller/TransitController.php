<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Attribute\Route;

class TransitController extends AbstractController
{

    public $request;
    public $statut_initial_libelle = 'Initial';
    public $statut_final_libelle = 'Finalisé';

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        return $this;
    }
}
