<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\VarDumper\Cloner\Data;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index( ProduitRepository $pr   ): Response
    {

        //recupération de toutes la table depuis la repository 
        $produits = $pr->findAll() ;



        return $this->render('produit/index.html.twig', [
         //envoie vers la Vue 
            "produits"=> $produits
        ]);
    }




    #[Route('/produit/add', name: 'app_produit_add')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        //new instance
        $produit = new Produit;

        //Formulaire 
        $form = $this->createForm(ProduitType::class, $produit);

        //Récupération des donnés via le formulaire
        //Injection de Request from httpFoundation 






        $form->handleRequest($request);


        if ($form->isSubmitted()  && $form->isValid()) {
           /////////////////////////////// //image ////////////////////////////
            $imageFile = $form->get('image')->getData();
             if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '.' . $imageFile->guessExtension();
    
                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_produit');
                }
    
                $produit->setImage($newFilename);
            }  
            
            /////////////////////////////////////
            //Injection de l entity manager Interface
            

            $currentdatetime = new DateTimeImmutable('now'); 

            $produit->setCreatedat($currentdatetime);

            $em->persist($produit); // Requéte pour Ajouter un entité a la DB
            $em->flush(); // Exécution de req 

            //redirection a votre route  souhaité 
          return $this->redirectToRoute('app_produit');

        }
        return $this->render('produit/add.html.twig', [
            //send a variable to a view 
            "form" => $form
        ]);
    }

        


 #[Route('/produit/edit/{id}', name: 'app_produit_edit')]
    public function edit(  int $id , ProduitRepository $pr , Request $request, EntityManagerInterface $em): Response
    {
        // Récuperation de entoté a partir de LURL (ID)
        $produit = $pr->find($id)  ;

        //Formulaire 
        $form = $this->createForm(ProduitType::class, $produit);

        //Récupération des donnés via le formulaire
        //Injection de Request from httpFoundation 

        $form->handleRequest($request);


        if ($form->isSubmitted()  && $form->isValid()) {
            //Injection de l entity manager Interface

            $em->persist($produit); // Requéte pour Ajouter un entité a la DB
            $em->flush(); // Exécution de req 


            //redirection a votre route  souhaité 
          return $this->redirectToRoute('app_produit');


        }


        return $this->render('produit/edit.html.twig', [
            //send a variable to a view 

            "form" => $form
        ]);
    }










    #[Route('/produit/show/{id}', name: 'app_produit_show')]
    public function show(  int $id , ProduitRepository $pr , Request $request, EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $produit = $pr->find($id)  ;
 


        return $this->render('produit/show.html.twig', [
         "produit" => $produit
         ]);
    }

    
    #[Route('/produit/delete/{id}', name: 'app_produit_delete')]
    public function delete(  int $id , ProduitRepository $pr , EntityManagerInterface $em): Response
    {
        // Récuperation de entité a partir de LURL (ID)
        $produit = $pr->find($id)  ;

         $em->remove($produit) ;
        $em->flush();


         //entitymanager interface 
         //perist + flush
         //remove + flush

         return $this->redirectToRoute('app_produit');
 



        
    }




}



