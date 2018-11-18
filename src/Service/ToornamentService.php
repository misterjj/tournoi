<?php

namespace App\Service;


use App\Exception\ToornamentException;
use App\Model\Toornament\Game;
use App\Model\Toornament\GameList;
use App\Model\Toornament\GameOpponent;
use App\Model\Toornament\GameOpponentsList;
use App\Model\Toornament\Match;
use App\Model\Toornament\MatchesList;
use App\Model\Toornament\Opponent;
use App\Model\Toornament\OpponetsList;
use App\Model\Toornament\Participant;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ToornamentService
{
    CONST TOORNAMENT_BASE_URL = 'https://api.toornament.com';

    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzleClient;
    /**
     * @var FilesystemCache
     */
    private $cache;
    /**
     * @var String
     */
    private $clientId;
    /**
     * @var String
     */
    private $clientSecret;
    /**
     * @var String
     */
    private $tournamentId;
    /**
     * @var String
     */
    private $tournamentStageId;
    /**
     * @var array
     */
    private $roundNames = [];

    /**
     * ToornamentService constructor.
     * @param \GuzzleHttp\Client $guzzle_client
     * @param String $apikey
     * @param String $clientId
     * @param String $clientSecret
     * @param String $tournamentId
     * @param String $tournamentStageId
     */
    function __construct(
        \GuzzleHttp\Client $guzzle_client,
        FilesystemCache $cache,
        String $apikey,
        String $clientId,
        String $clientSecret,
        String $tournamentId,
        String $tournamentStageId
    )
    {
        $this->guzzleClient = $guzzle_client;
        $this->cache = $cache;
        $this->apikey = $apikey;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tournamentId = $tournamentId;
        $this->tournamentStageId = $tournamentStageId;
//        $cache->clear();
    }

    /**
     * @return string
     * @throws ToornamentException
     */
    private function getAccessToken()
    {
        $cacheKey = 'toornament.oauth.accessToken';

        if (!$this->cache->has($cacheKey)) {
            dump('no cach apikey');

            $res = $this->guzzleClient->post(
                self::TOORNAMENT_BASE_URL . '/oauth/v2/token',
                [
                    'form_params' => [
                        'grant_type'    => 'client_credentials',
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope'         => 'organizer:view organizer:result'
                    ]
                ]
            );

            if ($res->getStatusCode() != 200) {
                throw new ToornamentException('Can\'t create access tokens', 500);
            }
            $response = json_decode($res->getBody(), true);
            if (!isset($response['access_token']) || !isset($response['expires_in'])) {
                throw new ToornamentException('Access token or expires_in is empty', 500);
            }
            $accessToken = $response['access_token'];

            $this->cache->set(
                $cacheKey,
                $accessToken,
                intval($response['expires_in']) - 10
            );
        } else {
            $accessToken = $this->cache->get($cacheKey);
        }

        return $accessToken;
    }

    /**
     * @param string $endpoint
     * @param string $range
     * @param array $param
     * @return mixed
     * @throws ToornamentException
     */
    private function toornamentApiGet(string $endpoint, string $range = "", array $param = [])
    {
        if (substr($endpoint, 0, 1) !== '/') {
            $endpoint = '/' . $endpoint;
        }

        try {
            $headers = [
                'X-Api-Key'     => $this->apikey,
                'Authorization' => 'Bearer ' . $this->getAccessToken(),
            ];
            if (!empty($range)) {
                $headers = $headers + [
                    'Range'     => $range,
                ];
            }
            $res = $this->guzzleClient->get(
                self::TOORNAMENT_BASE_URL . $endpoint,
                [
                    'headers'       => $headers,
                    'form_params'   => $param
                ]
            );
        } catch (RequestException $e) {
            $message = $e->getMessage();
            switch ($e->getCode()) {
                case 403:
                    $message = 'Invalid ApiKey';
                    break;
                case 401:
                    $message = 'Invalid accessToken';
                    break;
                case 400:
                    $resError = json_decode($e->getResponse()->getBody(), true);
                    $message = isset($resError['errors'][0]['message']) ? $resError['errors'][0]['message'] : $message;
                    break;
                case 416:
                    $message = 'Invalid range';
            }
            throw new ToornamentException($message, 500,  $e);
        }

        return json_decode($res->getBody(), true);
    }

    /**
     * @param string $roundId
     * @return string
     */
    private function getRoundName(string $roundId)
    {
        if (!isset($this->roundNames[$roundId])) {
            $round = $this->toornamentApiGet(
                '/viewer/v2/tournaments/' . $this->tournamentId . '/rounds/' . $roundId
            );
            $this->roundNames[$roundId] = $round['name'];
        }

        return $this->roundNames[$roundId];
    }

    /**
     * @param String $matchId
     * @return GameList
     */
    public function getGameList(String $matchId)
    {
        $cacheKey = 'toornament.match.' . $matchId .'.gameslist' ;

        if (!$this->cache->has($cacheKey)) {
            dump('no cach gamelist');
            $match = $this->getMatch($matchId);
            //Ugly hack because $match->getSettings['match_format'] is always null
            $numberOfGame = 3;
            if (in_array($match->getRoundName(), ['Finale winner bracket', 'Finale loser bracket', 'Grande finale'])) {
                $numberOfGame = 5;
            }

            $gameList = new GameList();
            for ($i = 1; $i <= $numberOfGame; $i++) {
                $game = $this->toornamentApiGet(
                    '/organizer/v2/tournaments/' . $this->tournamentId . '/matches/' . $matchId . '/games/' . $i
                );

                $gameOpponentsList = new GameOpponentsList();
                foreach ($game['opponents'] as $aOpponent) {
                    $opponent = new GameOpponent(
                        $aOpponent['number'],
                        $aOpponent['position'],
                        $aOpponent['result'],
                        $aOpponent['rank'],
                        $aOpponent['forfeit'],
                        $aOpponent['score'],
                        $aOpponent['properties']['character']

                    );
                    $gameOpponentsList->add($opponent);
                }
                $gameOpponentsList->sortWith(function ($a, $b) {
                    /** @var $a GameOpponent */
                    /** @var $b GameOpponent */
                    return $a->getNumber() <=> $b->getNumber();
                });
                $gameList->add(
                    new Game(
                        $game['number'],
                        $game['status'],
                        $gameOpponentsList
                    )
                );
            }

            $this->cache->set(
                $cacheKey,
                $gameList,
                60 * 5
            );
        } else {
            $gameList = $this->cache->get($cacheKey);
        }

        return $gameList;
    }

    public function getMatches()
    {
        $cacheKey = 'toornament.matches';

        if (!$this->cache->has($cacheKey)) {
            dump('no cache match');
            $matches = $this->toornamentApiGet(
                '/organizer/v2/tournaments/' . $this->tournamentId . '/matches',
                    'matches=0-99',
                    [
                        'stage_ids'     => $this->tournamentStageId
                    ]
            );

            $matchesList = new MatchesList();
            foreach ($matches as $key => $aMatch) {
                $opponentsList = new OpponetsList();
                foreach ($aMatch['opponents'] as $aOpponent) {
                    $participant = new Participant(
                        $aOpponent['participant']['id'],
                        $aOpponent['participant']['name']
                    );

                    $opponent = new Opponent(
                        $aOpponent['number'],
                        $aOpponent['position'],
                        $aOpponent['result'],
                        $aOpponent['rank'],
                        $aOpponent['forfeit'],
                        $aOpponent['score'],
                        $participant
                    );
                    $opponentsList->add($opponent);
                }
                $opponentsList->sortWith(function ($a, $b)
                {
                    /** @var $a Opponent */
                    /** @var $b Opponent */
                    return $a->getNumber() <=> $b->getNumber();
                });

                $match = new Match(
                    $aMatch['id'],
                    $aMatch['stage_id'],
                    $aMatch['group_id'],
                    $aMatch['round_id'],
                    $this->getRoundName($aMatch['round_id']),
                    $aMatch['number'],
                    $aMatch['type'],
                    $aMatch['status'],
                    $aMatch['settings'],
                    $aMatch['scheduled_datetime'] ? new \DateTime($aMatch['scheduled_datetime']) : null,
                    $aMatch['played_at'] ? new \DateTime($aMatch['played_at']) : null,
                    $aMatch['public_note'],
                    $aMatch['private_note'],
                    $opponentsList
//                    $this->getGameList($aMatch['id'], $aMatch['number'])
                );
                $matchesList->add($match);

                $this->cache->deleteItem('toornament.match.' . $match->getId() .'.gameslist');
            }

            $this->cache->set(
                $cacheKey,
                $matchesList,
                60 * 5
            );
        } else {
            $matchesList = $this->cache->get($cacheKey);
        }

//        dump($matchesList);

        return $matchesList;
    }

    /**
     * @param string $matchId
     * @return Match
     */
    public function getMatch(string $matchId)
    {
        /** @var $match Match */
        $match = $this->getMatches()->find(function($item) use ($matchId)
        {
            /** @var $item Match */
            return $item->getId() === $matchId;
        })->get();

        return $match;
    }
}
