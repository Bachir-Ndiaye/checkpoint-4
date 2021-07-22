<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
