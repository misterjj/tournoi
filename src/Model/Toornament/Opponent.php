<?php
namespace App\Model\Toornament;


class Opponent
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
     * @var string|null
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
     * @var Participant
     */
    private $participant;

    /**
     * Opponents constructor.
     * @param int $number
     * @param int $position
     * @param string $result
     * @param int|null $rank
     * @param bool $forfeit
     * @param int|null $score
     * @param Participant $participant
     */
    public function __construct($number, $position, $result, $rank, $forfeit, $score, Participant $participant)
    {
        $this->number = $number;
        $this->position = $position;
        $this->result = $result;
        $this->rank = $rank;
        $this->forfeit = $forfeit;
        $this->score = $score;
        $this->participant = $participant;
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
     * @return Opponent
     */
    public function setNumber(int $number): Opponent
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
     * @return Opponent
     */
    public function setPosition(int $position): Opponent
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getResult(): ?string
    {
        return $this->result;
    }

    /**
     * @param string $result
     * @return Opponent
     */
    public function setResult(?string $result): Opponent
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
     * @return Opponent
     */
    public function setRank(?int $rank)
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
     * @return Opponent
     */
    public function setForfeit(bool $forfeit): Opponent
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
     * @return Opponent
     */
    public function setScore(?int $score)
    {
        $this->score = $score;
        return $this;
    }

    /**
     * @return Participant
     */
    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    /**
     * @param Participant $participant
     * @return Opponent
     */
    public function setParticipant(Participant $participant): Opponent
    {
        $this->participant = $participant;
        return $this;
    }
}