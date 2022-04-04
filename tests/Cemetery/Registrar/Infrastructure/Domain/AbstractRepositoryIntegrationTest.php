<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractRepositoryIntegrationTest extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    protected function truncateEntities(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    protected function getRowCount(string $entityClass): int
    {
        return (int) $this->entityManager
            ->getRepository($entityClass)
            ->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    protected function getRemovedAtTimestampById(string $entityClass, string $id): ?string
    {
        return $this->entityManager
            ->getRepository($entityClass)
            ->createQueryBuilder('e')
            ->select('e.removedAt')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
