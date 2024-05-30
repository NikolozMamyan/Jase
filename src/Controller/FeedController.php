<?php

namespace App\Controller;

use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;
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
}
