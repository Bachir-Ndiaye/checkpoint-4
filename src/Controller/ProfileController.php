<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

/**
 * @Route("/profile", name="profile_")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/{id}", name="index")
     */
    public function index(User $user, UserRepository  $userRepository): Response
    {
        $role = $userRepository->findOneBy([
            'id' => $user->getId()
        ]);

        if($role->getRoles()[0] == "ROLE_ADMIN"){
            return $this->render('admin/index.html.twig', [
                'user' => $user
            ]);
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/{id}/students", name="students")
     * IsGranted("ROLE_ADMIN")
     */
    public function students(User $user, UserRepository $userRepository): Response
    {
        $students = $userRepository->findBy([
            'teacher' => $user->getId()
        ]);

        return $this->render('admin/students.html.twig', [
            'students' => $students
        ]);
    }
}
