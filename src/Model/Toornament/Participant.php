<?php

namespace App\Model\Toornament;


class Participant
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var |null
     */
    private $name;

    /**
     * Participant constructor.
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
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
     * @return Participant
     */
    public function setId(string $id): Participant
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Participant
     */
    public function setName(?string $name): Participant
    {
        $this->name = $name;
        return $this;
    }


}