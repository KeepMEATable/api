<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ApiResource
 */
class Holder implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(checkHost=true, checkMX=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Queue::class, mappedBy="holder")
     */
    private $waitingLines;

    public function __construct()
    {
        $this->waitingLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(){}

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void{}

    /**
     * @return Collection|Queue[]
     */
    public function getWaitingLines(): Collection
    {
        return $this->waitingLines;
    }

    public function addWaitingLine(Queue $waitingLine): void
    {
        if (!$this->waitingLines->contains($waitingLine)) {
            $this->waitingLines[] = $waitingLine;
            $waitingLine->setHolder($this);
        }
    }

    public function removeWaitingLine(Queue $waitingLine): void
    {
        if ($this->waitingLines->contains($waitingLine)) {
            $this->waitingLines->removeElement($waitingLine);
            if ($waitingLine->getHolder() === $this) {
                $waitingLine->setHolder(null);
            }
        }
    }
}
