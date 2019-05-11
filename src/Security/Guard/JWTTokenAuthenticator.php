<?php

declare(strict_types=1);

namespace App\Security\Guard;

use App\Entity\Holder;
use App\Exception\PaymentRequiredException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator as BaseAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JWTTokenAuthenticator extends BaseAuthenticator
{
    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        $user = parent::getUser($preAuthToken, $userProvider);

        if ($user instanceof Holder && Holder::STATUS_INACTIVE === $user->getStatus()) {
            throw new PaymentRequiredException();
        }

        return $user;
    }
}
