<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\ResetPassword;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ApiResource(
 *     messenger=true,
 *     collectionOperations = {
 *          "get" = {
 *              "access_control" = "is_granted('ROLE_ADMIN')"
 *          },
 *          "post" = {
 *              "status" = 202,
 *              "access_control" = "is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"
 *          }
 *     },
 *     itemOperations = {},
 *     input=ResetPassword::class,
 *     output=false
 * )
 */
class ResetPasswordRequest
{
    /**
     * @ORM\Id
     * @ORM\Column(nullable=true)
     */
    private $token;
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity=Holder::class)
     */
    private $user;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiresAt;

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function getUser(): ?Holder
    {
        return $this->user;
    }

    public function setUser(Holder $user): void
    {
        $this->user = $user;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }
}
