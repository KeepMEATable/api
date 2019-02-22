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
 *     accessControl="is_granted('ROLE_ADMIN')",
 *     collectionOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"Holder:read"}
 *             }
 *         },
 *         "post"={
 *             "denormalization_context"={
 *                 "groups"={"Holder:subscription"}
 *             }
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "normalization_context"={
 *                 "groups"={"Holder:read"}
 *             },
 *             "access_control"="is_granted('ROLE_ADMIN') or object.getId() == user.getId()"
 *         }
 *     }
 * )
 */
class Holder implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     * @Groups({"Holder:subscription", "Holder:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json", options={"default"="[]"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Groups("Holder:subscription")
     */
    private $password;

    /**
     * @Groups("Holder:subscription")
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity=Queue::class, mappedBy="holder")
     * @Groups("Holder:read")
     */
    private $waitingLines;

    public function __construct()
    {
        $this->waitingLines = new ArrayCollection();
    }

    public function getId(): ?string
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
    public function getSalt(): void
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

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
