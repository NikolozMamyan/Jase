<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Form\FeedType;
use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(EntityManagerInterface $entityManager, FeedRepository $feedRepo): Response
    {
        $feeds = $feedRepo->findAll();
        
        return $this->render('feed/index.html.twig', [
            'feeds' => $feeds,
        ]);
    }


    #[Route('/feed/new', name: 'app_feed_new')]
    public function new(EntityManagerInterface $entityManager, FeedRepository $feedRepo, Request $request): Response
    {
        $feed = new Feed;
        $form= $this->createForm(FeedType:: class, $feed);
        $form -> handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $entityManager->persist($feed);
            $entityManager->flush();

            // display message
            $this->addFlash('success', 'Votre blog a bien été crée.');

            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('feed/create.html.twig', [
            'form' => $form,
        ]);
    }
}
