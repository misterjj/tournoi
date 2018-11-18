<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class DefaultController
{
    /**
     * @Route("/", name="home")
     * @param Environment $twig
     * @return Response
     */
    public function index(Environment $twig)
    {
        return new Response($twig->render('default/index.html.twig'));
    }
}
