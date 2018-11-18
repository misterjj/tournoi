<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class RulesController
{
    /**
     * @Route("/rules", name="rules")
     * @param Environment $twig
     * @return Response
     */
    public function index(Environment $twig)
    {
        return new Response($twig->render('rules/index.html.twig'));
    }
}
