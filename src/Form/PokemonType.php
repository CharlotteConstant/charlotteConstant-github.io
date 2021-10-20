<?php

namespace App\Form;

use App\Entity\Pokemon;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;



class PokemonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('categorie', EntityType::class, [
                "class" => Category::class,
                "choice_label" => "name"
            ])
            ->add('description')
            ->add('image', FileType::class, 
            ['mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '2048k',
                    'mimeTypes' => [
                        'image/png', 'image/jpg', 'image/jpeg'
                    ],
                    'mimeTypesMessage' => "Boudiou c'est quoi? envoye une vraie image"
                ])


            ]
            
            
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pokemon::class,
        ]);
    }
}
