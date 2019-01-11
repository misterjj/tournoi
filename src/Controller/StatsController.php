<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class StatsController
{
    /**
     * @Route("/stats", name="rules")
     * @param Environment $twig
     * @return Response
     */
    public function index(Environment $twig)
    {
        return new Response($twig->render('stats/index.html.twig'));
    }
}
