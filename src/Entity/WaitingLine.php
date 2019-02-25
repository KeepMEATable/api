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
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ApiResource(
 *     mercure=true,
 *     messenger=true,
 *     collectionOperations={
 *         "get"={ "access_control"="is_granted('ROLE_HOLDER')" },
 *         "post"={
 *             "access_control"="is_granted('ROLE_CUSTOMER')"
 *         }
 *     },
 *     itemOperations={
 *         "get"={
 *             "access_control"="is_granted('ROLE_CUSTOMER')"
 *         }
 *     },
 *     normalizationContext={
 *         "groups"={"WaitingLine:read"}
 *     },
 *     denormalizationContext={
 *         "groups"={"WaitingLine:write"}
 *     }
 * )
 */
class WaitingLine
{
    private const WORKFLOW_MARKING_STARTED = 'started';
    private const WORKFLOW_MARKING_WAITING = 'waiting';
    private const WORKFLOW_MARKING_READY = 'ready';

    /**
     * @Groups({"WaitingLine:read", "WaitingLine:write"})
     * @ORM\Column(type="string", length=36, unique=true)
     * @ORM\Id
     */
    public $customerId;
    /**
     * @Groups("WaitingLine:read")
     * @ORM\Column(type="boolean", nullable=false, options={"default"=false})
     */
    public $started = true;
    /**
     * @Groups("WaitingLine:read")
     * @ORM\Column(type="boolean", nullable=false,  options={"default"=false})
     */
    public $waiting = false;
    /**
     * @Groups("WaitingLine:read")
     * @ORM\Column(type="boolean", nullable=false,  options={"default"=false})
     */
    public $ready = false;
    /**
     * @ORM\ManyToOne(targetEntity=Holder::class, inversedBy="waitingLines")
     */
    private $holder;

    public function getMarking(): string
    {
        switch (true) {
            case $this->started && !$this->waiting && !$this->ready:
                return self::WORKFLOW_MARKING_STARTED;
            case $this->started && $this->waiting && !$this->ready:
                return self::WORKFLOW_MARKING_WAITING;
            case $this->started && !$this->waiting && $this->ready:
                return self::WORKFLOW_MARKING_READY;
            default:
                throw new \LogicException('The queue is in a state that is not supported.');
        }
    }

    public function setMarking(string $state): void
    {
        switch ($state) {
            case self::WORKFLOW_MARKING_STARTED:
                $this->started = true;
                $this->waiting = $this->ready = false;
                break;
            case self::WORKFLOW_MARKING_WAITING:
                $this->started = $this->waiting = true;
                $this->ready = false;
                break;
            case self::WORKFLOW_MARKING_READY:
                $this->waiting = false;
                $this->started = $this->ready = true;
                break;
            default:
                throw new \LogicException(sprintf('The state %s is not supported.', $state));
        }
    }

    public function getHolder(): ?Holder
    {
        return $this->holder;
    }

    public function setHolder(?Holder $holder): void
    {
    }
}
