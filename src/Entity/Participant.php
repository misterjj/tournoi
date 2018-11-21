<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 * @UniqueEntity("mail")
 */
class Participant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Email(
     *     message = "l'email '{{ value }}' n'est pas valide.",
     *     checkMX = true
     * )
     * @Assert\Regex(
     *     pattern="/.*@ankama\.com/",
     *     message = "l'email '{{ value }}' n'est pas un email ankama.com",
     * )
     */
    private $mail;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAddedDate(): ?\DateTimeInterface
    {
        return $this->added_date;
    }

    public function setAddedDate(\DateTimeInterface $added_date): self
    {
        $this->added_date = $added_date;

        return $this;
    }
}
