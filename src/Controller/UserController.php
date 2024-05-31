<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user')]
    public function userFinded(UserRepository $userRepo, int $id): Response
    {
        $userFinded = $userRepo->find($id);
        

        return $this->render('user/index.html.twig', [
            'userFinded' => $userFinded,
        ]);
    }
}
