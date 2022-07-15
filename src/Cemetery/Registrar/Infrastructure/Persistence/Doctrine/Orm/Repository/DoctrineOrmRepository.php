<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\RepositoryValidator;
use Cemetery\Registrar\Infrastructure\Persistence\Repository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class DoctrineOrmRepository extends Repository
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param RepositoryValidator    $repositoryValidator
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        RepositoryValidator                     $repositoryValidator,
    ) {
        parent::__construct($repositoryValidator);
    }

    /**
     * {@inheritdoc}
     */
    public function save($aggregateRoot): void
    {
        $this->assertSupportedAggregateRootClass($aggregateRoot);
        $this->doSave($aggregateRoot);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function saveAll($aggregateRoots): void
    {
        $this->assertSupportedAggregateRootCollectionClass($aggregateRoots);
        foreach ($aggregateRoots as $aggregateRoot) {
            $this->doSave($aggregateRoot);
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
        $this->doRemove($aggregateRoot);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAll($aggregateRoots): void
    {
        $this->assertSupportedAggregateRootCollectionClass($aggregateRoots);
        foreach ($aggregateRoots as $aggregateRoot) {
            $this->doRemove($aggregateRoot);
        }
        $this->entityManager->flush();
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    private function doSave(AggregateRoot $aggregateRoot): void
    {
        $this->validateUniqueness($aggregateRoot);
        $this->validateReferences($aggregateRoot);
        $aggregateRoot->refreshUpdatedAtTimestamp();
        $this->entityManager->persist($aggregateRoot);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    private function doRemove(AggregateRoot $aggregateRoot): void
    {
        $this->validateInverseReferences($aggregateRoot);
        $aggregateRoot->refreshRemovedAtTimestamp();
        $this->entityManager->persist($aggregateRoot);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    private function validateUniqueness(AggregateRoot $aggregateRoot): void
    {
        $this->repositoryValidator()->validateUniqueness($aggregateRoot, $this);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    private function validateReferences(AggregateRoot $aggregateRoot): void
    {
        $this->repositoryValidator()->validateReferences($aggregateRoot, $this);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    private function validateInverseReferences(AggregateRoot $aggregateRoot): void
    {
        $this->repositoryValidator()->validateInverseReferences($aggregateRoot, $this);
    }
}
