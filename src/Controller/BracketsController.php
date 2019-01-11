<?php

namespace App\Controller;

use App\Model\Toornament\Match;
use App\Model\Toornament\MatchesList;
use App\Service\ToornamentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class BracketsController
{
    /**
     * @Route("/brackets", name="brackets")
     */
    public function index(Environment $twig, ToornamentService $toornament)
    {
        $matches = $toornament->getMatches();
        $groupIdWinner = getenv('TOORNAMENT_TOURRNAMENT_GROUP_ID_WINNNER');
        $groupIdLoser = getenv('TOORNAMENT_TOURRNAMENT_GROUP_ID_LOSER');

        $winnerBracket = $matches->filter(function ($match) use ($groupIdWinner)
        {
            /** @var $match Match */
            return $match->getGroupId() === $groupIdWinner;
        });
        $loserBracket = $matches->filter(function ($match) use ($groupIdLoser)
        {
            /** @var $match Match */
            return $match->getGroupId() === $groupIdLoser;
        });
        $finalBracket = new MatchesList();
        $finalBracket->add($matches->get(14));
        $finalBracket->add($matches->get($matches->count() - 2));
        $finalBracket->add($matches->get($matches->count() - 1));

        return new Response($twig->render('brackets/index.html.twig', [
            'winnerBracket' => $winnerBracket,
            'loserBracket' => $loserBracket,
            'finalBracket' => $finalBracket,
        ]));
    }

    /**
     * @Route("/gamesList/{matchId}", name="gamesList")
     */
    public function gamesList(Request $request, Environment $twig, ToornamentService $toornament)
    {
        $matchId = $request->attributes->get('matchId');

        return new Response($twig->render('brackets/modal-match.html.twig', [
            'match' => $toornament->getMatch($matchId),
            'gameList' => $toornament->getGameList($matchId),
        ]));
    }
}
