<?php

/*
 * This file is part of the "KeepMeATable" project.
 *
 * (c) Grégoire Hébert <gregoire@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ApiResource(
 *     itemOperations={
 *         "get"={
 *              "access_control"="object.getId() == user.getId()",
 *              "normalization_context" = {"groups"={"Holder:read"}}
 *         }
 *     }
 * )
 */
class Holder implements UserInterface
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"Holder:read", "WaitingLine:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="json", options={"default"="[]"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=WaitingLine::class, mappedBy="holder")
     * @Groups("Holder:read")
     */
    private $waitingLines;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var \DateTime
     * @Assert\DateTime
     * @ORM\Column(type="datetime", options={"default" = "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="string", options={"default"=Holder::STATUS_ACTIVE})
     */
    private $status;

    /**
     * @var int time in seconds
     * @ORM\Column(type="integer", options={"default"=1800})
     * @Groups({"WaitingLine:read"})
     */
    private $estimatedDelay = 1800;

    public function __construct()
    {
        $this->waitingLines = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_HOLDER';

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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    /**
     * @return Collection|WaitingLine[]
     */
    public function getWaitingLines(): Collection
    {
        return $this->waitingLines;
    }

    public function addWaitingLine(WaitingLine $waitingLine): void
    {
        if (!$this->waitingLines->contains($waitingLine)) {
            $this->waitingLines[] = $waitingLine;
            $waitingLine->setHolder($this);
        }
    }

    public function removeWaitingLine(WaitingLine $waitingLine): void
    {
        if ($this->waitingLines->contains($waitingLine)) {
            $this->waitingLines->removeElement($waitingLine);
            if ($waitingLine->getHolder() === $this) {
                $waitingLine->setHolder(null);
            }
        }
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
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
     */
    public function setStatus(string $status): void
    {
        if (!in_array($status, array(self::STATUS_ACTIVE, self::STATUS_INACTIVE), true)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        $this->status = $status;
    }

    public function getEstimatedDelay(): int
    {
        return $this->estimatedDelay;
    }

    public function setEstimatedDelay(int $estimatedDelay): void
    {
        $this->estimatedDelay = $estimatedDelay;
    }
}
