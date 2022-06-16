<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'categorie_liste')]
    public function index(ManagerRegistry $doctrine): Response // Manager nous permet de communiquer avce la base de données
    {
        $entityManager = $doctrine->getManager();
        $c = new Categorie();
        $form = $this->createForm(
            CategorieType::class,
            $c,
            array('action' => $this->generateUrl('categorie_add'))
        );
        $data['form'] = $form->createView();
        $data['categories'] = $entityManager->getRepository(Categorie::class)->findAll(); // Nous permet de faire la recherche d'une categorie dans la  base de données, une fois trouvé il affiche tout dans la base de données        return $this->render('categorie/index.html.twig', [
        return $this->render('categorie/liste.html.twig', $data);
    }

    #[Route('/Categorie/get{id}', name: 'categorie_get')]
    public function id()
    {
        return $this->render('categorie/liste.html.twig');
    }


    #[Route('/Categorie/add', name: 'categorie_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $c = new Categorie();
        $form = $this->createForm(CategorieType::class, $c);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $c = $form->getData();
            $c->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($c);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_liste');
    }

    #[Route('/Categorie/delete/{id}', name: 'categorie_delete')]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        //$entityManager = $this->getDoctrine()->getManager();
        $entityManager = $doctrine->getManager();

        $categorie = $entityManager->getRepository(Categorie::class)->find($id);
        if ($categorie != NULL) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }
        return $this->redirectToRoute('categorie_liste');
    }

    #[Route('/Categorie/edit/{id}', name: 'categorie_edit')]
    public function edit(ManagerRegistry $doctrine, $id): Response
    {
        $entityManager = $doctrine->getManager();
        $c = $entityManager->getRepository(Categorie::class)->find($id);

        $form = $this->createForm(CategorieType::class, $c, array('action' => $this->generateUrl('categorie_update', ['id' => $id])));
        $data['form'] = $form->createView();

        $data['categories'] = $entityManager->getRepository(Categorie::class)->findAll();
        return $this->render('categorie/liste.html.twig', $data);
    }

    #[Route('/Categorie/update/{id}', name: 'categorie_update')]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        //instancier de categorie
        $c = new Categorie();
        $form = $this->createForm(CategorieType::class, $c);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $c = $form->getData();
            // recuperer l'id du User 
            $c->setUser($this->getUser());
            $c->setId($id);
            // Récupération de la base de données
            $entityManager = $doctrine->getManager();
            $categorie = $entityManager->getRepository(Categorie::class)->find($id);
            $categorie->setNomCat($c->getNomCat());
            $entityManager->flush();
        }
        return $this->redirectToRoute('categorie_liste');
    }
}