<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Entity\Category;
use App\Form\PokemonType;
use App\Form\CategoryType;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PokemonController extends AbstractController
{
    /**
     * 
     * @Route("/pokemon", name="pokemon")
     * 
     */
    public function index(PokemonRepository $repo): Response
    {
        $imagesPokemons = $this->getParameter('images_pokemons');
        $pokemons = $repo->findAll();
        return $this->render('pokemon/index.html.twig', [
            'controller_name' => 'PokemonController', 'lesPokemons' => $pokemons, 'images' => $imagesPokemons
        ]);
    }

    /**
     * 
     * @Route("/pokemon/{id}", name="showPokemon", requirements={"id"="\d+"})
     * 
     */
    public function show(Pokemon $pokemon)
    {
        return $this->render('pokemon/pokemon.html.twig', ['lePokemon' => $pokemon]);

    }

    /**
     * 
     * @Route("/pokemon/new", name="newPokemon")
     * @Route("/pokemon/edit/{id}", name="editPokemon", requirements={"id"="\d+"})
     */
    public function new(Request $laRequete, EntityManagerInterface $entityManager, Pokemon $pokemon= null, SluggerInterface $slugger)
    {
        $modeCreation = false;
        if(!$pokemon){
        $pokemon = new Pokemon();
        $modeCreation = true;

        }
        $formulaire = $this->createForm(PokemonType::class, $pokemon);
        $formulaire->handleRequest($laRequete);
        if($formulaire->isSubmitted() && $formulaire->isValid()){
        $imageEnvoyee = $formulaire->get("image")->getData();
        if($imageEnvoyee){
            try {
            
           //creer nom unique pour cette image
            $newImageName = uniqid().'.'.$imageEnvoyee->guessExtension(); //trouve l'extension de mon img

           //deplacer l'image du dossier tmp ver le dossier images/pokemons
            $imageEnvoyee->move(
                //chemin cible
                $this->getParameter('images_pokemons'),
                //nom de la cible
                $newImageName
                );
                 //inserer l'image dans notre base de données
                 if($modeCreation || (!$modeCreation && $imageEnvoyee)){
                $pokemon->setImage($newImageName);
            }   

            } catch(FileException $e){
                throw $e;
                return $this->redirectToRoute('newPokemon');
            }
          
        }
        $entityManager->persist($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute("showPokemon", ["id" => $pokemon->getId()]);
        }
        return $this->render('pokemon/create.html.twig', ['formulairePokemon' =>$formulaire->createView(), 'creation'=>$modeCreation]);


    }
    /**
     * @Route("/pokemon/delete/{id}", name="deletePokemon")
     * 
     */
    public function delete(EntityManagerInterface $entityManager, Pokemon $pokemon)
    {
        $entityManager->remove($pokemon);
        $entityManager->flush();
        return $this->redirectToRoute("pokemon");
    }

    /**
     * 
     * @Route("/pokemon/category/new", name="newCategory")
     */
    public function newCategory(Request $requete, EntityManagerInterface $manager){
        $categorie = new Category();
        $formulaire = $this->createForm(CategoryType::class, $categorie);
        $formulaire->handleRequest($requete);

        if($formulaire->isSubmitted() && $formulaire->isValid()){
            $manager->persist($categorie);
            $manager->flush();
            return $this->redirectToRoute("pokemon");

        }

        return $this->render('pokemon/createCategory.html.twig', ['formulaireCategory' => $formulaire->createView()]);
    }


}
