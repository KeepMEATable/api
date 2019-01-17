<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ApiResource(
 *     collectionOperations={
 *          "get", "post"
 *     },
 *     itemOperations={
 *          "get"
 *     }
 * )
 */
class Queue
{
    /**
     * @ORM\Column(type="string", length=36)
     * @ORM\Id
     */
    public $customerId;
    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    public $started = false;
    /**
     * @ORM\Column(type="boolean", nullable=false,  options={"default": false})
     */
    public $waiting = false;
    /**
     * @ORM\Column(type="boolean", nullable=false,  options={"default": false})
     */
    public $ready = false;
}
