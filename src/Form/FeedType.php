<?php

namespace App\Form;

use App\Entity\Feed;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FeedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'required' => true,
            'label' => 'Titre'
        ])
            ->add('Description', TextType::class)
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image du cours',
                'label_attr'=> [
                    'class' => 'form-label mt-4 fw-bold'
                ],
                'required'=> true,
                'download_uri' => false,
            ])
           
            ->add('save', SubmitType::class, ['label' => "Enregistrer"]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feed::class,
        ]);
    }
}
