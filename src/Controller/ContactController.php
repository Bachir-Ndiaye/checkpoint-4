<?php

namespace App\Controller;

use App\Form\ContactFormType;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);

        // TODO : Traiter le formulaire de contact
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setEmail($_POST['contact_form']['email']);
            $contact->setMessage($_POST['contact_form']['message']);
            $contact->setFullname($_POST['contact_form']['fullname']);

            $manager->persist($contact);
            $manager->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé. Vous serez contactés dans les meilleurs délais');
            return $this->render('home/index.html.twig');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
