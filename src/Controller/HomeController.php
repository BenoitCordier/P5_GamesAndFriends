<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Home page access
     *
     * @return Response
     */
    #[Route('/', name: 'home.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/home/index.html.twig');
    }

    /**
     * Map page access
     *
     * @return Response
     */
    #[Route('/map', name: 'home.map', methods: ['GET'])]
    public function map(): Response
    {
        return $this->render('pages/home/map.html.twig');
    }
}
