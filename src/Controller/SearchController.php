<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function searchBar(Request $request)
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('handleSearch'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control p-2 rounded-4 me-2 rounded shadow-sm',
                    'placeholder' => 'Recherchez un utilisateur'
                ]
            ])
            ->getForm();
    
        // Ajoutez ceci pour désactiver Turbo pour ce formulaire
        $form->setData($request->query->get('query'));
        $form->handleRequest($request);
    
        return $this->render('search/searchBar.html.twig', [
            'form' => $form->createView(),
            'turbo' => false, // Ajoutez cette option pour désactiver Turbo
        ]);
    }

    #[Route('/handleSearch', name: 'handleSearch')]
    public function handleSearch(Request $request, UserRepository $repo): Response
    {

        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('handleSearch'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Recherchez un utilisateur'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-secondary'
                ]
            ])->getForm();

        $form->handleRequest($request);

        $query = $form->getData()['query'];

        $users = [];

        if ($query) {
            $users= $repo->findUserByName($query);
           
        }

        return $this->render('search/index.html.twig', [
            'users' => $users,
        ]);
    }
}
