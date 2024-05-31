<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Follow;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user')]
    public function userFinded(UserRepository $userRepo, int $id, EntityManagerInterface $entityManager): Response
    {
        $userFinded = $userRepo->find($id);
        $currentUser = $this->getUser();
        $isFollowing = false;

        if ($currentUser && $userFinded) {
            $followRepository = $entityManager->getRepository(Follow::class);
            $follow = $followRepository->findOneBy([
                'follower' => $currentUser,
                'followed' => $userFinded,
            ]);

            $isFollowing = $follow !== null;
        }

        return $this->render('user/index.html.twig', [
            'userFinded' => $userFinded,
            'isFollowing' => $isFollowing,
        ]);
    }


    #[Route('/user/{id}/follow', name: 'app_follow_user')]
    public function followUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo, int $id): RedirectResponse
    {
        // Récupérer l'ID de l'utilisateur à suivre à partir de la requête
        $userIdToFollow = $userRepo->find($id);
      
    
        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();
    
        // Récupérer l'utilisateur à suivre à partir de son ID
        $userToFollow = $entityManager->getRepository(User::class)->find($userIdToFollow);
    
        // Vérifier si l'utilisateur à suivre existe
        if (!$userToFollow) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
    
        // Vérifier si l'utilisateur actuel suit déjà l'utilisateur à suivre
        $existingFollow = $entityManager->getRepository(Follow::class)->findOneBy([
            'follower' => $currentUser,
            'followed' => $userToFollow,
        ]);
    
        // Si l'utilisateur actuel suit déjà l'utilisateur à suivre, rediriger avec un message d'avertissement
        if ($existingFollow) {
            $this->addFlash('warning', 'Tu suis déjà cet utilisateur');
        } else {
            // Créer une nouvelle instance de Follow
            $follow = new Follow();
    $follow->setFollower($currentUser);
    $follow->setFollowed($userToFollow);

    // Récupérer le nombre actuel de followers
    $currentFollowers = $userToFollow->getFollowers();

    // Incrémenter le nombre de followers
    $newFollowers = $currentFollowers + 1;

    // Assigner la nouvelle valeur
    $userToFollow->setFollowers($newFollowers);

    // Persister le follow et les modifications sur l'utilisateur suivi
    $entityManager->persist($follow);
    $entityManager->persist($userToFollow);
    $entityManager->flush();

    // Afficher un message de succès
    $this->addFlash('success', 'Tu suis maintenant cet utilisateur');
        }
    
        // Rediriger vers une autre page, par exemple la page de profil de l'utilisateur suivi
        return $this->redirectToRoute('app_user', ['id' => $id]);
    }



    #[Route('/user/{id}/unfollow', name: 'app_unfollow_user')]
public function unfollowUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo, int $id): RedirectResponse
{
    $userIdToUnfollow = $userRepo->find($id);
    $currentUser = $this->getUser();

    if (!$currentUser || !$userIdToUnfollow) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    $follow = $entityManager->getRepository(Follow::class)->findOneBy([
        'follower' => $currentUser,
        'followed' => $userIdToUnfollow,
    ]);

    if ($follow) {
        $entityManager->remove($follow);
        $entityManager->flush();

        // Décrémenter le nombre de followers de l'utilisateur suivi
        $currentFollowers = $userIdToUnfollow->getFollowers();
        $newFollowers = $currentFollowers - 1;
        $userIdToUnfollow->setFollowers($newFollowers);
        $entityManager->persist($userIdToUnfollow);
        $entityManager->flush();

        $this->addFlash('success', 'Tu ne suis plus cet utilisateur');
    } else {
        $this->addFlash('warning', 'Tu ne suis pas cet utilisateur');
    }

    return $this->redirectToRoute('app_user', ['id' => $id]);
}
}
