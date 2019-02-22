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

    public function __invoke(Queue $updatedStatus): void
    {
        $this->manager->merge($updatedStatus);
        $this->manager->flush();
    }
}
