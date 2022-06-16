<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function login(): Response
    {
        return $this->render('login/index.html.twig');
    }

    public function logon()
    {
        // redirects to the "homepage" route
        return $this->redirectToRoute('accueil');
    }
}