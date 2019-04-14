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
use App\DTO\ResetPassword;
use App\Validator\Constraints as AppAssert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ApiResource(
 *     messenger=true,
 *     collectionOperations={
 *         "post"={
 *             "status"=202,
 *             "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *             "denormalization_context"={ "groups"={"ResetPasswordRequest:post"} }
 *         }
 *     },
 *     itemOperations={
 *         "put"={
 *             "status"=202,
 *             "path"="/reset_password/{id}",
 *             "access_control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *             "denormalization_context"={ "groups"={"ResetPasswordRequest:put"} }
 *         }
 *     },
 *     input=ResetPassword::class,
 *     output=false
 * )
 * @AppAssert\ExpiredResetPasswordToken
 */
class ResetPasswordRequest
{
    /**
     * @ORM\Id
     * @ORM\Column(nullable=true)
     */
    private $token;
    /**
     * @ORM\OneToOne(targetEntity=Holder::class)
     */
    private $user;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiresAt;

    private $newPassword;

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

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }
}
