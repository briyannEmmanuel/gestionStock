<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Produit;
use App\Form\SortieType;
use Doctrine\ORM\Mapping\OneToMany;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SortieController extends AbstractController
{
    #[Route('/Sortie/liste', name: 'sortie_liste')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // creates a task object and initializes some data for this example
        $s = new Sortie();

        $form = $this->createForm(SortieType::class, $s, array('action' => $this->generateUrl('sortie_add')));
        $data['form'] = $form->createView();

        $data['sorties'] = $entityManager->getRepository(Sortie::class)->findAll();

        return $this->render('sortie/liste.html.twig', $data);
    }

    #[Route('/Sortie/add', name: 'sortie_add')]
    public function add(ManagerRegistry $doctrine, Request $request): Response
    {
        $s = new Sortie();
        $form = $this->createForm(SortieType::class, $s);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $s = $form->getData();
            $s->setUser($this->getUser());
            // recuperation de l'id du User 
            $s->setUser($this->getUser());
            $qsortie = $s->getQteS();
            $p = $entityManager->getRepository(Produit::class)->find($s->getProduit()->getId());
            if ($p->getQtStock() < $s->getQteS()) {
                $s = new Sortie();
                $form = $this->createForm(SortieType::class, $s, array('action' => $this->generateUrl('sortie_add')));
                $data['form'] = $form->createView();
                $data['sorties'] = $entityManager->getRepository(Sortie::class)->findAll();
                $data['error_message'] = 'Le stock disponible est inferieur à ' . $qsortie;
                return $this->render('sortie/liste.html.twig', $data);
            } else {
                $entityManager->persist($s);
                $entityManager->flush();
                // Mise a jour du produit 
                $Stock = $p->getQtStock() - $s->getQteS();
                $p->setQtStock($Stock);
                $entityManager->flush();

                return $this->redirectToRoute('sortie_liste');
            }
        }
    }
}