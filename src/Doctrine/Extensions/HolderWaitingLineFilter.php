<?php

declare(strict_types=1);

namespace App\Doctrine\Extensions;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Holder;
use App\Entity\Queue;
use App\Workflow\Exceptions\MissingUserException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HolderWaitingLineFilter implements QueryCollectionExtensionInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $token = $this->tokenStorage->getToken();

        if (null === $token) {
            throw new MissingUserException('Cannot filter the collection without user.');
        }

        $user = $token->getUser();

        if ($user instanceof Holder && Queue::class === $resourceClass) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder->andWhere(sprintf('%s.holder = :current_user', $rootAlias));
            $queryBuilder->andWhere(sprintf('%s.waiting = :waiting', $rootAlias));
            $queryBuilder->setParameter('current_user', $user->getId());
            $queryBuilder->setParameter('waiting', true);
        }
    }
}
