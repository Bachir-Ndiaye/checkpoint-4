<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notice;
use App\Form\MessageType;
use App\Form\NoticeType;
use App\Repository\NoticeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/{id}/notes", name="notes")
     * IsGranted("ROLE_ADMIN")
     */
    public function notes(User $user, NoticeRepository  $noticeRepository): Response
    {
        $notes = $noticeRepository->findBy([
            'teacher' => $user->getId()
        ]);

        return $this->render('admin/notes.html.twig', [
            'notes' => $notes
        ]);
    }

    /**
     * @Route("/{id}/collegues", name="collegues")
     */
    public function collegues(User $user, UserRepository  $userRepository): Response
    {

        $collegues = $userRepository->findStudentsFromSameTeacher($user->getTeacher()->getId());

        return $this->render('profile/collegues.html.twig', [
            'collegues' => $collegues
        ]);
    }

    /**
     * @Route("/{id}/messages", name="send", methods={"GET","POST"})
     */
    public function send(Request $request, User $user, EntityManagerInterface $manager, UserRepository $userRepository): Response
    {
        $role = $userRepository->findOneBy([
            'id' => $user->getId()
        ]);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            //Send message to recipient

            $recipient = $userRepository->findOneBy([
                'id' => $_POST['message']['recipient']
            ]);

            $message->setCreatedAt(new \DateTime('now'));
            $message->setMessage($_POST['message']['message']);
            $message->setTitle('title');
            $message->setIsRead(false);
            $message->setSender($this->getUser());
            $message->setRecipient($recipient);

            $this->addFlash('success', 'Votre message a bien été envoyé');

            // Notify the recipient

            $manager->persist($message);
            $manager->flush();

            $this->redirectToRoute('profile_index',[
                'id' => $this->getUser()->getId()
            ]);
        }

        if($role->getRoles()[0] == "ROLE_ADMIN"){
            return $this->render('admin/messages.html.twig', [
                'form' => $form->createView(),
                'received' => $user->getReceived()->getValues(),
                'sent' => $user->getSent()->getValues()
            ]);
        }

        return $this->render('profile/messages.html.twig', [
            'form' => $form->createView(),
            'received' => $user->getReceived()->getValues(),
            'sent' => $user->getSent()->getValues()
        ]);
    }

    /**
     * @Route("/{id}/notice", name="notice", methods={"GET","POST"})
     */
    public function notice(Request $request, User $user, EntityManagerInterface $manager, UserRepository $userRepository){

        $notice = new Notice();
        $form = $this->createForm(NoticeType::class, $notice);

        if($_SERVER['REQUEST_METHOD'] === "POST"){

            $teacher = $user->getTeacher();

            $notice->setNote($_POST['notice']['note']);
            $notice->setDescription($_POST['notice']['description']);
            $notice->setPseudo($this->getUser());
            $notice->setTeacher($teacher);

            $this->addFlash('success', 'Votre d\'avoir donné votre avis');
            $manager->persist($notice);
            $manager->flush();


            // Notifier son prof

            return $this->render('profile/index.html.twig',[
                'user' => $user
            ]);
        }

        return $this->render('profile/notice.html.twig',[
            'form' => $form->createView()
        ]);
    }

}
