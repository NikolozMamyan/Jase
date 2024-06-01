<?php

namespace App\Controller;

use App\Entity\Feed;
use App\Entity\Like;
use App\Form\FeedType;
use App\Entity\Comment;
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

    #[Route('/feed/{id}/liked', name: 'app_feed_liked')]
    public function feedLiked(EntityManagerInterface $entityManager, FeedRepository $feedRepo, int $id): Response
    {
        $user = $this->getUser();
        $feed = $feedRepo->find($id);
    
        if (!$feed) {
            throw $this->createNotFoundException('Publication non trouvée');
        }
    
        // Vérifier si l'utilisateur a déjà liké ce feed
        $existingLike = $entityManager->getRepository(Like::class)->findOneBy(['user' => $user, 'feed' => $feed]);
    
        if ($existingLike) {
            // Supprimer le like existant
            $entityManager->remove($existingLike);
            $entityManager->flush();
         
        } else {
            // Ajouter un nouveau like
            $like = new Like();
            $like->setUser($user);
            $like->setFeed($feed);
    
            $entityManager->persist($like);
            $entityManager->flush();
           
        }
    
        return $this->redirectToRoute('app_feed');
    }

    #[Route('/feed/{id}/comment', name: 'app_feed_comment')]
    public function addComment(Request $request, $id, EntityManagerInterface $entityManager, FeedRepository $feedRepository): Response
    {
        $feed = $feedRepository->find($id);

        if (!$feed) {
            throw $this->createNotFoundException('Publication non trouvée');
        }

        $user = $this->getUser();
        $content = $request->request->get('comment');

        if (!$content) {
            $this->addFlash('error', 'Veuillez entrer un commentaire.');
        } else {
            $comment = new Comment();
            $comment->setContent($content);
            $comment->setUserCommented($user);
            $comment->setFeed($feed);
            // $comment->setCreatedAt(new \DateTime());
 

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été ajouté.');
        }

        return $this->redirectToRoute('app_feed', ['id' => $id]);
    }
    
    
        
    
}
