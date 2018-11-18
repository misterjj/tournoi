<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 10/11/2018
 * Time: 22:38
 */

namespace App\Model\Toornament;


class Game
{
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $status;

    /**
     * @var GameOpponentsList
     */
    private $opponents;

    /**
     * Game constructor.
     * @param int $number
     * @param string $status
     * @param GameOpponentsList $opponents
     */
    public function __construct($number, $status, GameOpponentsList $opponents)
    {
        $this->number = $number;
        $this->status = $status;
        $this->opponents = $opponents;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return Game
     */
    public function setNumber(int $number): Game
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Game
     */
    public function setStatus(string $status): Game
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return GameOpponentsList
     */
    public function getOpponents(): GameOpponentsList
    {
        return $this->opponents;
    }

    /**
     * @param GameOpponentsList $opponents
     * @return Game
     */
    public function setOpponents(GameOpponentsList $opponents): Game
    {
        $this->opponents = $opponents;
        return $this;
    }
}