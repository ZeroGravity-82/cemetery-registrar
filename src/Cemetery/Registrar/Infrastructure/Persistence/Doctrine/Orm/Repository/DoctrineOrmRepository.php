<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\AggregateRootCollection;
use Cemetery\Registrar\Domain\Model\EntityId;
use Cemetery\Registrar\Domain\Model\Repository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineOrmRepository implements Repository
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

    /**
     * Checks whether the aggregate root is of a type supported by the repository.
     *
     * @param AggregateRoot $aggregateRoot
     *
     * @throws \InvalidArgumentException when the aggregate root type does not match the repository
     */
    private function assertSupportedAggregateRootClass(AggregateRoot $aggregateRoot): void
    {
        $supportedAggregateRootClassName = $this->supportedAggregateRootClassName();
        if (!$aggregateRoot instanceof $supportedAggregateRootClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an aggregate root: expected "%s", "%s" given.',
                $this->supportedAggregateRootClassName(),
                \get_class($aggregateRoot)
            ));
        }
    }

    /**
     * Checks whether the aggregate root ID is of a type supported by the repository.
     *
     * @param EntityId $aggregateRootId
     *
     * @throws \InvalidArgumentException when the aggregate root ID type does not match the repository
     */
    private function assertSupportedAggregateRootIdClass(EntityId $aggregateRootId): void
    {
        $supportedAggregateRootIdClassName = $this->supportedAggregateRootIdClassName();
        if (!$aggregateRootId instanceof $supportedAggregateRootIdClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an aggregate root ID: expected "%s", "%s" given.',
                $this->supportedAggregateRootIdClassName(),
                \get_class($aggregateRootId)
            ));
        }
    }

    /**
     * Checks whether the aggregate root collection is of a type supported by the repository.
     *
     * @param AggregateRootCollection $aggregateRoots
     *
     * @throws \InvalidArgumentException when the aggregate root collection type does not match the repository
     */
    private function assertSupportedAggregateRootCollectionClass(AggregateRootCollection $aggregateRoots): void
    {
        $supportedAggregateRootCollectionClassName = $this->supportedAggregateRootCollectionClassName();
        if (!$aggregateRoots instanceof $supportedAggregateRootCollectionClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an aggregate root collection: expected "%s", "%s" given.',
                $this->supportedAggregateRootCollectionClassName(),
                \get_class($aggregateRoots)
            ));
        }
    }
}
