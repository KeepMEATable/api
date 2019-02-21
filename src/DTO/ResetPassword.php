<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;

final class ResetPassword
{
    /**
     * @var string the user's email
     * @Groups("ResetPasswordRequest:post")
     */
    public $email;
    /**
     * @var string new password
     * @Groups("ResetPasswordRequest:put")
     */
    public $password;
}
