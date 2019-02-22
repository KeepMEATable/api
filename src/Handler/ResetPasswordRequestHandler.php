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

namespace App\Handler;

use App\Entity\ResetPasswordRequest;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ResetPasswordRequestHandler implements MessageHandlerInterface
{
    private $mailer;
    private $registry;

    public function __construct(\Swift_Mailer $mailer, RegistryInterface $registry)
    {
        $this->mailer = $mailer;
        $this->registry = $registry;
    }

    public function __invoke(ResetPasswordRequest $passwordToken): void
    {
        if (null === $passwordToken->getToken()) {
            $this->createToken($passwordToken);
        } else {
            $this->updatePassword($passwordToken);
        }
    }

    private function createToken(ResetPasswordRequest $passwordToken): void
    {
        $em = $this->registry->getEntityManagerForClass(ResetPasswordRequest::class);

        if (null === $em) {
            throw new \RuntimeException('Cannot persist reset password request');
        }

        $user = $passwordToken->getUser();

        if (null === $user) {
            throw new \RuntimeException('Cannot find user.');
        }

        $passwordToken->setToken((Uuid::uuid4())->toString());
        $passwordToken->setExpiresAt(new \DateTime('1 day'));

        $em->persist($passwordToken);
        $em->flush();

        $message = (new \Swift_Message('Forgot Password'))
            ->setFrom('no-reply@keepmeatable.com')
            ->setTo($user->getEmail())
            ->setBody(<<<HTML
resetPassword by clicking here <a href="/forgot_password/{$passwordToken->getToken()}"></a>
HTML
            )
        ;

        $this->mailer->send($message);
    }

    private function updatePassword(ResetPasswordRequest $passwordToken): void
    {
        $em = $this->registry->getEntityManagerForClass(ResetPasswordRequest::class);

        if (null === $em) {
            throw new \RuntimeException('Cannot reset password.');
        }

        $user = $passwordToken->getUser();

        if (null === $user) {
            throw new \RuntimeException('Cannot find user.');
        }

        $newPassword = $passwordToken->getNewPassword();
        if (!\is_string($newPassword)) {
            throw new \LogicException('Cannot set a null password.');
        }

        $user->setPassword($newPassword);
        $passwordToken = $em->merge($passwordToken);

        $em->remove($passwordToken);
        $em->flush();

        $message = (new \Swift_Message('Forgot Password'))
            ->setFrom('no-reply@keepmeatable.com')
            ->setTo($passwordToken->getUser()->getEmail())
            ->setBody(<<<HTML
<p>Your password has been updated.</p>
HTML
            )
        ;

        $this->mailer->send($message);
    }
}
