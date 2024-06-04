<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Message;
use App\Form\MessageType;
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


    #[Route('/message/send/{id}', name: 'app_message_new')]

    public function sendMessage(Request $request, User $recipient, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($this->getUser());
            $message->setRecipient($recipient);
            $message->setSentAt(new DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message envoyé avec succès !');

            return $this->redirectToRoute('app_message', ['id' => $recipient->getId()]);
        }

        return $this->render('message/send.html.twig', [
            'form' => $form->createView(),
            'recipient' => $recipient,
        ]);
    }

    
}
