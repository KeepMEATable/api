<?php

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

    public function __invoke(ResetPasswordRequest $passwordToken)
    {
        if (null === $passwordToken->getToken()) {
            $this->createToken($passwordToken);
        } else {
            $this->updatePassword($passwordToken);
        }
    }

    private function createToken(ResetPasswordRequest $passwordToken)
    {
        $passwordToken->setToken((string) Uuid::uuid4());

        $passwordToken->setExpiresAt(new \DateTime('1 day'));
        $em = $this->registry->getEntityManagerForClass(ResetPasswordRequest::class);

        if (null === $em) {
            throw new \RuntimeException('Cannot persist reset password request');
        }

        $em->persist($passwordToken);
        $em->flush();

        $message = (new \Swift_Message('Forgot Password'))
            ->setFrom('no-reply@keepmeatable.com')
            ->setTo($passwordToken->getUser()->getEmail())
            ->setBody(<<<HTML
resetPassword by clicking here <a href="/forgot_password/{$passwordToken->getToken()}"></a>
HTML
            )
        ;

        $this->mailer->send($message);
    }

    private function updatePassword(ResetPasswordRequest $passwordToken)
    {
        $passwordToken->getUser()->setPassword($passwordToken->getNewPassword());
        $em = $this->registry->getEntityManagerForClass(ResetPasswordRequest::class);

        if (null === $em) {
            throw new \RuntimeException('Cannot persist reset password request');
        }

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
