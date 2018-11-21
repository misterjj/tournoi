<?php

namespace App\Controller;

use App\Model\Toornament\Match;
use App\Model\Toornament\MatchesList;
use App\Service\ToornamentService;
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
    public function index(Environment $twig, ToornamentService $toornament)
    {
        $matches = $toornament->getMatches();
        $finalBracket = new MatchesList();
        $finalBracket->add($matches->get($matches->count() - 3));
        $finalBracket->add($matches->get($matches->count() - 2));
        $finalBracket->add($matches->get($matches->count() - 1));

        return new Response(
            $twig->render('default/index.html.twig' , [
                'nextMatches' => $toornament->getNextMatch(6),
                'finalBracket' => $finalBracket,
            ])
        );
    }
}
