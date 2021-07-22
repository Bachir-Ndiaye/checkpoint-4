<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PedagogyController extends AbstractController
{
    /**
     * @Route("/pedagogy", name="pedagogy")
     */
    public function index(): Response
    {
        return $this->render('pedagogy/index.html.twig', [
            'controller_name' => 'PedagogyController',
        ]);
    }
}
