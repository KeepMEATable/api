<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ResetPasswordRequestHandler implements MessageHandlerInterface
{
    private $mailer;
    private $em;

    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->em = $em;
    }

    public function __invoke(ResetPasswordRequest $passwordToken)
    {
        $passwordToken->setToken((string) Uuid::uuid4());

        $passwordToken->setExpiresAt(new \DateTime('1 day'));
        $this->em->persist($passwordToken);
        $this->em->flush();

        $message = (new \Swift_Message('Forgot Password'))
            ->setFrom('no-reply@keepmeatable.dev')
            ->setTo($passwordToken->getUser()->getEmail())
            ->setBody(<<<HTML
resetPassword by clicking here <a href="/forgot_password/{$passwordToken->getToken()}"></a>
HTML
            )
        ;

        $this->mailer->send($message);
    }
}
