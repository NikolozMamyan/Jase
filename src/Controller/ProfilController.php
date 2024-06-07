<?php

namespace App\Controller;

use App\Form\AvatarType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user= $this->getUser();
        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profil/avatar', name: 'app_profil_avatar')]
    public function avatar(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user= $this->getUser();
        $form = $this->createForm(AvatarType::class, $user);
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
                $user->setImageName($newFilename);
            }
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('profil/avatar.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}


