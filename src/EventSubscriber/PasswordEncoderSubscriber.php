<?php

namespace App\EventSubscriber;

use App\Entity\Holder;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordEncoderSubscriber implements EventSubscriber
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /** @var Holder $entity */
        $entity = $args->getObject();
        $plainPassword = $entity->getPlainPassword();

        if (!$entity instanceof Holder && null !== $plainPassword) {
            return;
        }

        $entity->setPassword($this->passwordEncoder->encodePassword($entity, $plainPassword));
    }
}
