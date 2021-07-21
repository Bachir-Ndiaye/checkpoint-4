<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Redirect according if there is a user logged in or not
     * 
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        // If user => redirect to user profile
        // else redirect to form to log in
        if($this->getUser()){
            $this->redirectToRoute('profile_index',[
                'id' => $this->getUser()->getId()
            ]);
        }else{
            $this->redirectToRoute('app_login');
        }
        
        return $this->render('home/index.html.twig', [
           
        ]);
    }
}
