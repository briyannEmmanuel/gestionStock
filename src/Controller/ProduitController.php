<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ProduitController extends AbstractController
{
    #[Route('/Produit/liste', name: 'produit_liste')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $p = new Produit();
        $form = $this->createForm(
            ProduitType::class,
            $p,
            array('action' => $this->generateUrl('produit_add'))
        );
        $data['form'] = $form->createView();

        $data['produits'] = $entityManager->getRepository(Produit::class)->findAll();
        return $this->render('produit/liste.html.twig', $data);
    }


    #[Route('/Produit/get{id}', name: 'produit_get')]
    public function id()
    {
        return $this->render('produit/liste.html.twig');
    }


    #[Route('/Produit/add', name: 'produit_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $p = new Produit();
        $form = $this->createForm(ProduitType::class, $p);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $p = $form->getData();
            $p->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($p);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_liste');
    }

    #[Route('/Produit/delete/{id}', name: 'produit_delete')]
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        //$entityManager = $this->getDoctrine()->getManager();
        $entityManager = $doctrine->getManager();

        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if ($produit != NULL) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }
        return $this->redirectToRoute('produit_liste');
    }

    #[Route('/Produit/edit/{id}', name: 'produit_edit')]
    public function edit(ManagerRegistry $doctrine, $id): Response
    {

        $entityManager = $doctrine->getManager();
        $p = $entityManager->getRepository(Produit::class)->find($id);

        $form = $this->createForm(ProduitType::class, $p, array('action' => $this->generateUrl('produit_update', ['id' => $id])));
        $data['form'] = $form->createView();

        $data['produits'] = $entityManager->getRepository(Produit::class)->findAll();
        return $this->render('produit/liste.html.twig', $data);
    }

    #[Route('/Produit/update/{id}', name: 'produit_update')]
    public function update(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        //instancier le produit
        $p = new Produit();
        $form = $this->createForm(ProduitType::class, $p);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $p = $form->getData();
            // recuperer l'id du User 
            $p->setUser($this->getUser());
            $p->setId($id);
            // Récupération de la base de données
            $entityManager = $doctrine->getManager();
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            $produit->setLibelle($p->getLibelle());
            $produit->setQtStock($p->getQtStock());
            $produit->setCategorie($p->getCategorie());
            $entityManager->flush();
        }
        return $this->redirectToRoute('produit_liste');
    }
}