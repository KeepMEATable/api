<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Holder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class HolderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Holder::class);
    }

    public function findOneBy(array $criteria, array $orderBy = null): ?Holder
    {
        return parent::findOneBy($criteria, $orderBy);
    }
}