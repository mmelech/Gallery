<?php

namespace App\Entity;

use App\Repository\UserDataRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Class UserData.
 */
#[ORM\Entity(repositoryClass: UserDataRepository::class)]
#[ORM\Table(name: 'users_data')]
#[UniqueEntity(fields: ['login'])]
class UserData
{
    /**
     * Primary key.
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * Login.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $login;

    /**
     * Firstname.
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private $firstname;

    /**
     * Getter for Id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for login.
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * Setter for login.
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * Getter for firstname.
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Setter for firstname.
     */
    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }
}
