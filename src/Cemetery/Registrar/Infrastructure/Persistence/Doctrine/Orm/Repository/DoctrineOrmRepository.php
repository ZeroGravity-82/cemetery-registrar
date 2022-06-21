<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Repository as RepositoryInterface;
use Cemetery\Registrar\Infrastructure\Persistence\Repository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineOrmRepository extends Repository implements RepositoryInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function save($aggregateRoot): void
    {
        $this->assertSupportedAggregateRootClass($aggregateRoot);
        $aggregateRoot->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($aggregateRoot);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll($aggregateRoots): void
    {
        $this->assertSupportedAggregateRootCollectionClass($aggregateRoots);
        foreach ($aggregateRoots as $aggregateRoot) {
            $aggregateRoot->refreshUpdatedAtTimestamp();
            $this->entityManager->persist($aggregateRoot);
        }
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findById($aggregateRootId): ?AggregateRoot
    {
        $this->assertSupportedAggregateRootIdClass($aggregateRootId);
        return $this->entityManager->getRepository($this->supportedAggregateRootClassName())->findBy([
            'id'        => $aggregateRootId->value(),
            'removedAt' => null,
        ])[0] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($aggregateRoot): void
    {
        $this->assertSupportedAggregateRootClass($aggregateRoot);
        $aggregateRoot->refreshRemovedAtTimestamp();
        $this->entityManager->persist($aggregateRoot);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll($aggregateRoots): void
    {
        $this->assertSupportedAggregateRootCollectionClass($aggregateRoots);
        foreach ($aggregateRoots as $aggregateRoot) {
            $aggregateRoot->refreshRemovedAtTimestamp();
            $this->entityManager->persist($aggregateRoot);
        }
        $this->entityManager->flush();
    }
}
