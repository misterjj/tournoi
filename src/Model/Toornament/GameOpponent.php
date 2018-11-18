<?php
namespace App\Model\Toornament;


class GameOpponent
{
    CONST RESULT_WIN = 'win';
    CONST RESULT_DRAW = 'draw';
    CONST RESULT_LOSS = 'loss';

    /**
     * @var int
     */
    private $number;

    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $result;

    /**
     * @var int|null
     */
    private $rank;

    /**
     * @var boolean
     */
    private $forfeit;

    /**
     * @var int|null
     */
    private $score;

    /**
     * @var string|null
     */
    private $character;

    /**
     * Opponents constructor.
     * @param int $number
     * @param int $position
     * @param string $result
     * @param int|null $rank
     * @param bool $forfeit
     * @param int|null $score
     * @param string $character
     */
    public function __construct($number, $position, $result, $rank, $forfeit, $score, $character)
    {
        $this->number = $number;
        $this->position = $position;
        $this->result = $result;
        $this->rank = $rank;
        $this->forfeit = $forfeit;
        $this->score = $score;
        $this->character = $character;
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
     * @return GameOpponent
     */
    public function setNumber(int $number): GameOpponent
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     * @return GameOpponent
     */
    public function setPosition(int $position): GameOpponent
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getResult(): string
    {
        return $this->result;
    }

    /**
     * @param string $result
     * @return GameOpponent
     */
    public function setResult(string $result): GameOpponent
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getRank(): ?int
    {
        return $this->rank;
    }

    /**
     * @param int|null $rank
     * @return GameOpponent
     */
    public function setRank(?int $rank): GameOpponent
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * @return bool
     */
    public function isForfeit(): bool
    {
        return $this->forfeit;
    }

    /**
     * @param bool $forfeit
     * @return GameOpponent
     */
    public function setForfeit(bool $forfeit): GameOpponent
    {
        $this->forfeit = $forfeit;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getScore(): ?int
    {
        return $this->score;
    }

    /**
     * @param int|null $score
     * @return GameOpponent
     */
    public function setScore(?int $score): GameOpponent
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getCharacter(): ?string
    {
        return $this->character;
    }

    /**
     * @param null|string $character
     * @return GameOpponent
     */
    public function setCharacter(?string $character): GameOpponent
    {
        $this->character = $character;
        return $this;
    }
}