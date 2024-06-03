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
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(EntityManagerInterface $entityManager, FeedRepository $feedRepo, SluggerInterface $slugger, Request $request): Response
    {
        $user = $this->getUser();
        $feeds = $feedRepo->findAllOrderedByDate();
        $feed = new Feed();
        $form = $this->createForm(FeedType::class, $feed);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imgFile */
            $imgFile = $form->get('imageFile')->getData();
    
            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imgFile->guessExtension();
    
                // Déplacer le fichier vers le répertoire où les images sont stockées
                try {
                    $imgFile->move($this->getParameter('feed_images_directory'), $newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se produit pendant le téléchargement du fichier
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
    
                // Mettre à jour la propriété 'imageName' pour stocker le nom du fichier image
                $feed->setImageName($newFilename);
            }
    
            $feed->setAuthor($user);
            $entityManager->persist($feed);
            $entityManager->flush();
    
            // Afficher un message de succès
            $this->addFlash('success', 'Votre blog a bien été créé.');
    
            return $this->redirectToRoute('app_feed');
        
      
   
    }
    return $this->render('feed/index.html.twig', [
        'feeds' => $feeds,
        'form' => $form->createView(),
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
            $feed->removeLike($existingLike);
            $entityManager->remove($existingLike);
        } else {
            // Ajouter un nouveau like
            $like = new Like();
            $like->setUser($user);
            $like->setFeed($feed);
            $entityManager->persist($like);
            $feed->addLike($like);
        }

        $entityManager->flush();

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
