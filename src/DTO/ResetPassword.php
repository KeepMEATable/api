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
