<?php

namespace App\Controller;

use App\Entity\Entree;
use App\Entity\Produit;
use App\Form\EntreeType;
use App\Form\ProduitType;
use Doctrine\ORM\Mapping\OneToMany;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntreeController extends AbstractController
{
    #[Route('/Entree/liste', name: 'entree_liste')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $e = new Entree();
        $form = $this->createForm(
            EntreeType::class,
            $e,
            array('action' => $this->generateUrl('entree_add'))
        );
        $data['form'] = $form->createView();

        $data['entrees'] = $entityManager->getRepository(Entree::class)->findAll();
        return $this->render('entree/liste.html.twig', $data);
    }

    #[Route('/Entree/add', name: 'entree_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $e = new Entree();
        $form = $this->createForm(EntreeType::class, $e);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $e = $form->getData();
            $e->setUser($this->getUser());
            $entityManager = $doctrine->getManager();
            $entityManager->persist($e);
            $entityManager->flush();
            // Mise à jour des produits
            $p = $entityManager->getRepository(Produit::class)->find($e->getProduit()->getId());
            $stock = $p->getQtStock() + $e->getQteE();
            $p->setQtStock($stock);
            $entityManager->flush();
        }

        return $this->redirectToRoute('entree_liste');
    }
}