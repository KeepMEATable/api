<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExpiredResetPasswordToken extends Constraint
{
    public $message = 'This token is expired.';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
