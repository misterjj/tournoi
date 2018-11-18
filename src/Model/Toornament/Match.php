<?php

namespace App\Model\Toornament;

use \DateTime;

class Match
{
    const TYPE_DUEL = 'duel';
    const TYPE_FFA = 'ffa';
    const TYPE_BYE = 'bye';
    const STATUS_PENDING = 'pending';
    const STATUS_RUNNING = 'running';
    const STATUS_COMPLETED = 'completed';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $stageId;

    /**
     * @var string
     */
    private $groupId;

    /**
     * @var string
     */
    private $roundId;

    /**
     * @var string
     */
    private $roundName;

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var DateTime|null
     */
    private $scheduled_datetime;

    /**
     * @var DateTime|null
     */
    private $played_at;

    /**
     * @var string|null
     */
    private $publicNote;

    /**
     * @var string|null
     */
    private $privateNote;

    /**
     * @var OpponetsList
     */
    private $opponents;

    /**
     * Match constructor.
     * @param string $id
     * @param string $stageId
     * @param string $groupId
     * @param string $roundId
     * @param string $roundName
     * @param string $numberId
     * @param string $type
     * @param string $status
     * @param string $settings
     * @param null|DateTime $scheduled_datetime
     * @param null|DateTime $played_at
     * @param null|string $publicNote
     * @param null|string $privateNote
     * @param OpponetsList $opponents
     */
    public function __construct(
        $id,
        $stageId,
        $groupId,
        $roundId,
        $roundName,
        $numberId,
        $type,
        $status,
        $settings,
        $scheduled_datetime,
        $played_at,
        $publicNote,
        $privateNote,
        OpponetsList $opponents
    )
    {
        $this->id = $id;
        $this->stageId = $stageId;
        $this->groupId = $groupId;
        $this->roundId = $roundId;
        $this->roundName = $roundName;
        $this->number = $numberId;
        $this->type = $type;
        $this->status = $status;
        $this->settings = $settings;
        $this->scheduled_datetime = $scheduled_datetime;
        $this->played_at = $played_at;
        $this->publicNote = $publicNote;
        $this->privateNote = $privateNote;
        $this->opponents = $opponents;
//        $this->games = $games;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Match
     */
    public function setId(string $id): Match
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getStageId(): string
    {
        return $this->stageId;
    }

    /**
     * @param string $stageId
     * @return Match
     */
    public function setStageId(string $stageId): Match
    {
        $this->stageId = $stageId;
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     * @return Match
     */
    public function setGroupId(string $groupId): Match
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoundId(): string
    {
        return $this->roundId;
    }

    /**
     * @param string $roundId
     * @return Match
     */
    public function setRoundId(string $roundId): Match
    {
        $this->roundId = $roundId;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoundName(): string
    {
        return $this->roundName;
    }

    /**
     * @param string $roundName
     * @return Match
     */
    public function setRoundName(string $roundName): Match
    {
        $this->roundName = $roundName;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return Match
     */
    public function setNumber(string $number): Match
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Match
     */
    public function setType(string $type): Match
    {
        $this->type = $type;
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
     * @return Match
     */
    public function setStatus(string $status): Match
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     * @return Match
     */
    public function setSettings(array $settings): Match
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getScheduledDatetime(): ?DateTime
    {
        return $this->scheduled_datetime;
    }

    /**
     * @param null|DateTime $scheduled_datetime
     * @return Match
     */
    public function setScheduledDatetime(?DateTime $scheduled_datetime)
    {
        $this->scheduled_datetime = $scheduled_datetime;
        return $this;
    }

    /**
     * @return null|DateTime
     */
    public function getPlayedAt(): ?DateTime
    {
        return $this->played_at;
    }

    /**
     * @param null|DateTime $played_at
     * @return Match
     */
    public function setPlayedAt(?DateTime $played_at)
    {
        $this->played_at = $played_at;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPublicNote(): ?string
    {
        return $this->publicNote;
    }

    /**
     * @param null|string $publicNote
     * @return Match
     */
    public function setPublicNote(?string $publicNote)
    {
        $this->publicNote = $publicNote;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPrivateNote(): ?string
    {
        return $this->privateNote;
    }

    /**
     * @param null|string $privateNote
     * @return Match
     */
    public function setPrivateNote(?string $privateNote)
    {
        $this->privateNote = $privateNote;
        return $this;
    }

    /**
     * @return OpponetsList
     */
    public function getOpponents(): OpponetsList
    {
        return $this->opponents;
    }

    /**
     * @param OpponetsList $opponents
     * @return Match
     */
    public function setOpponents(OpponetsList $opponents): Match
    {
        $this->opponents = $opponents;
        return $this;
    }
}