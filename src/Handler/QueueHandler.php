<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Queue;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class QueueHandler implements MessageHandlerInterface
{
    private $manager;

    public function __construct(ManagerRegistry $registry)
    {
        $this->manager = $registry->getManagerForClass(Queue::class);
    }

    public function __invoke(Queue $updatedStatus)
    {
        dump($updatedStatus);
        $this->manager->merge($updatedStatus);
        $this->manager->flush();
    }
}
