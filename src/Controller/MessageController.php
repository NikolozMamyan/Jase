<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(MessageRepository $messageRepository): Response
    {
        $user = $this->getUser();
        $sentMessages = $messageRepository->findBy(['sender' => $user]);
        $receivedMessages = $messageRepository->findBy(['recipient' => $user]);

        return $this->render('message/index.html.twig', [
            'sentMessages' => $sentMessages,
            'receivedMessages' => $receivedMessages,
        ]);
   
    }


    #[Route('/messages/box/{id}', name: 'app_messages_between')]
    public function getMessagesBetween(MessageRepository $messageRepository, Request $request, UserRepository $UserRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $currentUser = $this->getUser();
        $otherUser = $UserRepository->find($id);

        if (!$otherUser) {
            throw $this->createNotFoundException('L\'utilisateur n\'existe pas');
        }

        $messages = $messageRepository->findMessagesBetween($currentUser, $otherUser);

        $newMessage = new Message();
        $form = $this->createForm(MessageType::class, $newMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newMessage->setSender($currentUser);
            $newMessage->setRecipient($otherUser);
            $newMessage->setSentAt(new \DateTime());

            $entityManager->persist($newMessage);
            $entityManager->flush();

        }

        return $this->render('message/messages.html.twig', [
            'messages' => $messages,
            'otherUser' => $otherUser,
            'form' => $form->createView(),
        ]);
    }
}
    

