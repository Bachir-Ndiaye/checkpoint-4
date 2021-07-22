<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Check if there is a user already logged in
        if ($this->getUser()) {
             return $this->redirectToRoute('profile_index',[
                 'id' => $this->getUser()->getId()
             ]);
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        // Check if there is a user already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('profile_index',[
                'id' => $this->getUser()->getId()
            ]);
        }


        // If no user => generate form to register and handle form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user, $_POST['user']['password']);
            $user->setPassword($hash);
            $user->setAvatar($user->getAvatar());
            $user->setEmail($_POST['user']['email']);
            $user->setFirstname($_POST['user']['firstname']);
            $user->setLastname($_POST['user']['lastname']);
            $user->setRoles(["ROLE_USER"]);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Votre compte a été crée avec succès');

            //Automatic login after registration
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            $this->container->get('session')->set('_security_main', serialize($token));

            return $this->render('home/index.html.twig',[

            ]);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/check/", name="login_check")
     */
    public function loginCheck(): Response
    {
       return $this->redirectToRoute('profile_index',[
           'id' => $this->getUser()->getId()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
