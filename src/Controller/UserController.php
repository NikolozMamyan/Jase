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
        $userToFollow = $userRepo->find($id);
        $currentUser = $this->getUser();

        if (!$userToFollow) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $existingFollow = $entityManager->getRepository(Follow::class)->findOneBy([
            'follower' => $currentUser,
            'followed' => $userToFollow,
        ]);

        if ($existingFollow) {
            $this->addFlash('warning', 'Tu suis déjà cet utilisateur');
        } else {
            $follow = new Follow();
            $follow->setFollower($currentUser);
            $follow->setFollowed($userToFollow);
            $currentUser->addFollowing($follow);
            $userToFollow->addFollower($follow);

            $entityManager->persist($follow);
            $entityManager->flush();

            $this->addFlash('success', 'Tu suis maintenant cet utilisateur');
        }

        return $this->redirectToRoute('app_user', ['id' => $id]);
    }

    #[Route('/user/{id}/unfollow', name: 'app_unfollow_user')]
    public function unfollowUser(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepo, int $id): RedirectResponse
    {
        $userToUnfollow = $userRepo->find($id);
        $currentUser = $this->getUser();

        if (!$currentUser || !$userToUnfollow) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $follow = $entityManager->getRepository(Follow::class)->findOneBy([
            'follower' => $currentUser,
            'followed' => $userToUnfollow,
        ]);

        if ($follow) {
            $currentUser->removeFollowing($follow);
            $userToUnfollow->removeFollower($follow);
            $entityManager->remove($follow);
            $entityManager->flush();

            $this->addFlash('success', 'Tu ne suis plus cet utilisateur');
        } else {
            $this->addFlash('warning', 'Tu ne suis pas cet utilisateur');
        }

        return $this->redirectToRoute('app_user', ['id' => $id]);
    }
}
