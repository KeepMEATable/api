<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExpiredResetPasswordTokenValidator extends ConstraintValidator
{
    public function validate($resetPasswordRequest, Constraint $constraint): void
    {
        if (!$constraint instanceof ExpiredResetPasswordToken) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\ExpiredResetPasswordToken');
        }

        if (null !== $resetPasswordRequest->getExpiresAt() && new \DateTime() > $resetPasswordRequest->getExpiresAt()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
