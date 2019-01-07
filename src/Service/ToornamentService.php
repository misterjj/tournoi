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
use Symfony\Component\Config\Definition\Exception\Exception;

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
     * ToornamentService constructor.
     * @param \GuzzleHttp\Client $guzzle_client
     * @param FilesystemCache $cache
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
     * @param bool $async
     * @return \GuzzleHttp\Promise\PromiseInterface|mixed
     * @throws ToornamentException
     */
    private function toornamentApiGet(string $endpoint, string $range = "", array $param = [], $async = false)
    {
        if (substr($endpoint, 0, 1) !== '/') {
            $endpoint = '/' . $endpoint;
        }

        $headers = [
            'X-Api-Key'     => $this->apikey,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
        if (!empty($range)) {
            $headers = $headers + [
                    'Range'     => $range,
                ];
        }
        if (!$async) {
            try {
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
        } else {
            $promise = $this->guzzleClient->getAsync(
                self::TOORNAMENT_BASE_URL . $endpoint,
                [
                    'headers'       => $headers,
                    'form_params'   => $param
                ]
            );

            return $promise;
        }
    }

    /**
     * @param array $roundIds
     * @return array
     */
    private function getRoundNames(array $roundIds)
    {
        $promises = [];
        foreach ($roundIds as $roundId)
        {
            $promises[$roundId] = $this->toornamentApiGet(
                '/viewer/v2/tournaments/' . $this->tournamentId . '/rounds/' . $roundId,
                "",
                [],
                true
            );
        }
        $results = \GuzzleHttp\Promise\unwrap($promises);

        $roundNames = [];
        foreach ($results as $result) {
            /** @var $result \GuzzleHttp\Psr7\Response */
            $round = json_decode($result->getBody(), true);
            $roundNames[$round['id']] = $round['name'];
        }

        return $roundNames;
    }

    /**
     * @param String $matchId
     * @return GameList
     */
    public function getGameList(String $matchId)
    {
        $cacheKey = 'toornament.match.' . $matchId .'.gameslist' ;

        if (!$this->cache->has($cacheKey)) {
            $match = $this->getMatch($matchId);
            //Ugly hack because $match->getSettings['match_format'] is always null
            $numberOfGame = 3;
            if (in_array($match->getRoundName(), ['Finale winner bracket', 'Finale loser bracket', 'Grande finale'])) {
                $numberOfGame = 5;
            }

            $promises = [];
            for ($i = 1; $i <= $numberOfGame; $i++) {
                $promises['game' . $i] = $this->toornamentApiGet(
                    '/organizer/v2/tournaments/' . $this->tournamentId . '/matches/' . $matchId . '/games/' . $i,
                    "",
                    [],
                    true
                );
            }

            try {
                $results = \GuzzleHttp\Promise\settle($promises)->wait();
                ksort($results);
            } catch (Exception $e) {
                dump('ici');
                dump($e);
                exit;
            }


            $gameList = new GameList();
            foreach ($results as $result) {
                if (!isset($result['value'])) {
                    continue;
                }
                /** @var $result \GuzzleHttp\Psr7\Response */
                $result = $result['value'];
                $game = json_decode($result->getBody(), true);

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

    /**
     * @return MatchesList
     */
    public function getMatches()
    {
        $cacheKey = 'toornament.matches';

        if (!$this->cache->has($cacheKey)) {
            $matches = $this->toornamentApiGet(
                '/organizer/v2/tournaments/' . $this->tournamentId . '/matches',
                    'matches=0-99',
                    [
                        'stage_ids'     => $this->tournamentStageId
                    ]
            );

            $roundIds = [];
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

                if (!in_array($aMatch['round_id'], $roundIds)) {
                    $roundIds[] = $aMatch['round_id'];
                }
                $match = new Match(
                    $aMatch['id'],
                    $aMatch['stage_id'],
                    $aMatch['group_id'],
                    $aMatch['round_id'],
                    "",
                    $aMatch['number'],
                    $aMatch['type'],
                    $aMatch['status'],
                    $aMatch['settings'],
                    $aMatch['scheduled_datetime'] ? new \DateTime($aMatch['scheduled_datetime']) : null,
                    $aMatch['played_at'] ? new \DateTime($aMatch['played_at']) : null,
                    $aMatch['public_note'],
                    $aMatch['private_note'],
                    $opponentsList
                );
                $matchesList->add($match);

                $this->cache->deleteItem('toornament.match.' . $match->getId() .'.gameslist');
            }

            $roundNames = $this->getRoundNames($roundIds);
            foreach ($matchesList as $match) {
                /** @var $match Match */
                if (key_exists($match->getRoundId(), $roundNames)) {
                    $match->setRoundName($roundNames[$match->getRoundId()]);
                }
            }
            $this->cache->set(
                $cacheKey,
                $matchesList,
                60 * 5
            );
        } else {
            $matchesList = $this->cache->get($cacheKey);
        }

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

    /**
     * @param int $number
     * @return MatchesList
     */
    public function getNextMatch(int $number)
    {
        /** @var MatchesList $nextMatch */
        $nextMatch =  $this->getMatches()
            ->filter(function ($match) {
                /** @var $match Match */
                return $match->getStatus() !== Match::STATUS_COMPLETED;
            });

       $nextMatch
            ->sortWith(function ($a, $b) {
                /** @var $a Match */
                /** @var $b Match */
                if (is_null($a->getScheduledDatetime())) {
                    return -1;
                } else if (is_null($b->getScheduledDatetime())) {
                    return 1;
                }
                return $a->getScheduledDatetime()->getTimestamp() <=> $b->getScheduledDatetime()->getTimestamp();
            });

        return $nextMatch->take($number);
    }
}
