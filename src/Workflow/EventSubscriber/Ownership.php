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

namespace App\Workflow\EventSubscriber;

use App\Entity\Holder;
use App\Entity\WaitingLine;
use App\Workflow\Exceptions\MissingUserException;
use App\Workflow\Exceptions\NotHandledUserException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Workflow\Event\Event;

class Ownership implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function setOwnership(Event $event): void
    {
        $waitingLine = $event->getSubject();

        if (!$waitingLine instanceof WaitingLine) {
            return;
        }

        $user = $this->getUser();

        $waitingLine->setHolder($user);
        $waitingLine->setAwaitStartedAt(new \DateTime());
    }

    public function removeOwnership(Event $event): void
    {
        $waitingLine = $event->getSubject();

        if (!$waitingLine instanceof WaitingLine) {
            return;
        }

        $waitingLine->setHolder(null);
        $waitingLine->setAwaitStartedAt(null);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.queue.entered.waiting' => 'setOwnership',
            'workflow.queue.entered.started' => 'removeOwnership',
        ];
    }

    private function getUser(): Holder
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            throw new MissingUserException('Trying to set ownership to an unknown user.');
        }

        $user = $token->getUser();

        if (!$user instanceof Holder) {
            throw new NotHandledUserException('This type of user cannot be set as owner.');
        }

        return $user;
    }
}
