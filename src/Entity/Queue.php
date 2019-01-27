<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ApiResource(
 *     mercure=true,
 *     messenger=true,
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"Queue:read"}},
 * )
 * @ApiFilter(BooleanFilter::class, properties={"waiting"})
 */
class Queue
{
    private const WORKFLOW_MARKING_STARTED = 'started';
    private const WORKFLOW_MARKING_WAITING = 'waiting';
    private const WORKFLOW_MARKING_READY = 'ready';

    /**
     * @Groups("Queue:read")
     * @ORM\Column(type="string", length=36)
     * @ORM\Id
     */
    public $customerId;
    /**
     * @Groups("Queue:read")
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    public $started = true;
    /**
     * @Groups("Queue:read")
     * @ORM\Column(type="boolean", nullable=false,  options={"default": false})
     */
    public $waiting = false;
    /**
     * @Groups("Queue:read")
     * @ORM\Column(type="boolean", nullable=false,  options={"default": false})
     */
    public $ready = false;

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

    public function setMarking(string $state) :void
    {
        switch ($state) {
            case self::WORKFLOW_MARKING_STARTED;
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
}
