<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Infrastructure\Persistence\Repository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineOrmRepository extends Repository
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     * @throws Exception                 when unique constraints (if any) are violated
     */
    public function save($aggregateRoot): void
    {
        $this->assertSupportedAggregateRootClass($aggregateRoot);
        $aggregateRoot->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($aggregateRoot);
        $this->entityManager->flush();
    }

    /**
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     * @throws Exception                 when unique constraints (if any) are violated
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
     * @throws \InvalidArgumentException when the aggregate root ID type does not match the repository
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
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     */
    public function remove($aggregateRoot): void
    {
        $this->assertSupportedAggregateRootClass($aggregateRoot);
        $aggregateRoot->refreshRemovedAtTimestamp();
        $this->entityManager->persist($aggregateRoot);
        $this->entityManager->flush();
    }

    /**
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
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
