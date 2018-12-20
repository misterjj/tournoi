<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CashprizeController
{
    /**
     * @Route("/", name="cashprize")
     * @param Environment $twig
     * @return Response
     */
    public function index(Environment $twig)
    {
        return new Response(
            $twig->render('cashprize/index.html.twig')
        );
    }
}
