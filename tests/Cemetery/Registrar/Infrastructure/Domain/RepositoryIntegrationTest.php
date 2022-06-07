<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain;

use Cemetery\Registrar\Domain\Entity;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class RepositoryIntegrationTest extends KernelTestCase
{
    protected Entity                 $entityA;
    protected Entity                 $entityB;
    protected Entity                 $entityC;
    protected EntityManagerInterface $entityManager;
    protected object                 $repo;
    protected string                 $entityClassName;
    protected string                 $entityIdClassName;
    protected string                 $entityCollectionClassName;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->truncateEntities();
    }

    public function testItSavesANewEntity(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->entityA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntity = $this->repo->findById($this->entityA->id());
        $this->assertNotNull($persistedEntity);
        $this->assertTrue($this->areEqualEntities($persistedEntity, $this->entityA));
        $this->assertTrue($this->areEqualDateTimeValues($this->entityA->createdAt(), $persistedEntity->createdAt()));
        $this->assertTrue($this->areEqualDateTimeValues($this->entityA->updatedAt(), $persistedEntity->updatedAt()));
        $this->assertNull($persistedEntity->removedAt());

        $this->assertSame(1, $this->getRowCount($this->entityClassName));
    }

    public function testItReturnsNullIfAnEntityIsNotFoundById(): void
    {
        $entity = $this->repo->findById(new $this->entityIdClassName('unknown_id'));
        $this->assertNull($entity);
    }

    public function testItSavesACollectionOfNewEntities(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntity = $this->repo->findById($this->entityA->id());
        $this->assertNotNull($persistedEntity);
        $this->assertTrue($this->areEqualEntities($persistedEntity, $this->entityA));

        $persistedEntity = $this->repo->findById($this->entityB->id());
        $this->assertNotNull($persistedEntity);
        $this->assertTrue($this->areEqualEntities($persistedEntity, $this->entityB));

        $persistedEntity = $this->repo->findById($this->entityC->id());
        $this->assertNotNull($persistedEntity);
        $this->assertTrue($this->areEqualEntities($persistedEntity, $this->entityC));

        $this->assertSame(3, $this->getRowCount($this->entityClassName));
    }

    public function testItUpdatesAlreadyPersistedEntity(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->entityA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->updateEntityA($persistedEntityA);
        $updatedEntity = $persistedEntityA;
        sleep(1);                                   // for correct updatedAt timestamp
        $this->repo->save($persistedEntityA);
        $this->entityManager->clear();

        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->assertTrue($this->areEqualEntities($persistedEntityA, $updatedEntity));
        $this->assertTrue($this->areEqualDateTimeValues($this->entityA->createdAt(), $persistedEntityA->createdAt()));
        $this->assertTrue($this->entityA->updatedAt() < $persistedEntityA->updatedAt());
        $this->assertNull($persistedEntityA->removedAt());

        $this->assertSame(1, $this->getRowCount($this->entityClassName));
    }

    public function testItUpdatesAlreadyPersistedEntityWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->updateEntityA($persistedEntityA);
        $updatedEntityA = $persistedEntityA;
        sleep(1);                                   // for correct updatedAt timestamp
        $this->repo->saveAll(new $this->entityCollectionClassName([$persistedEntityA, $this->entityC]));
        $this->entityManager->clear();

        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->assertTrue($this->areEqualEntities($persistedEntityA, $updatedEntityA));
        $this->assertTrue($this->areEqualDateTimeValues($this->entityA->createdAt(), $persistedEntityA->createdAt()));
        $this->assertTrue($this->entityA->updatedAt() < $persistedEntityA->updatedAt());
        $this->assertNull($persistedEntityA->removedAt());

        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $this->assertTrue($this->areEqualEntities($persistedEntityB, $this->entityB));

        $persistedEntityC = $this->repo->findById($this->entityC->id());
        $this->assertTrue($this->areEqualEntities($persistedEntityC, $this->entityC));

        $this->assertSame(3, $this->getRowCount($this->entityClassName));
    }

    public function testItRemovesAnEntity(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->entityA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->repo->remove($persistedEntityA);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->entityA->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById($this->entityClassName, $this->entityA->id()->value()));

        $this->assertSame(1, $this->getRowCount($this->entityClassName));
    }

    public function testItRemovesACollectionOfEntities(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $persistedEntityC = $this->repo->findById($this->entityC->id());
        $this->repo->removeAll(new $this->entityCollectionClassName([$persistedEntityB, $persistedEntityC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->entityB->id()));
        $this->assertNull($this->repo->findById($this->entityC->id()));
        $this->assertNotNull($this->repo->findById($this->entityA->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById($this->entityClassName, $this->entityB->id()->value()));
        $this->assertNotNull($this->getRemovedAtTimestampById($this->entityClassName, $this->entityC->id()->value()));
        $this->assertNull($this->getRemovedAtTimestampById($this->entityClassName, $this->entityA->id()->value()));

        $this->assertSame(3, $this->getRowCount($this->entityClassName));
    }

    /**
     * Checks that the entities have the same values for all of their properties.
     *
     * @param Entity $entityOne
     * @param Entity $entityTwo
     *
     * @return bool
     */
    abstract protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool;

    /**
     * Updates some properties of the entity A.
     *
     * @param Entity $entityA
     */
    abstract protected function updateEntityA(Entity $entityA): void;

    protected function truncateEntities(): void
    {
        (new OrmPurger($this->entityManager))->purge();
    }

    protected function areSameClasses(Entity $entityOne, Entity $entityTwo): bool
    {
        return $entityOne instanceof $this->entityClassName && $entityTwo instanceof $this->entityClassName;
    }

    protected function areEqualValueObjects(?object $propertyOne, ?object $propertyTwo): bool
    {
        return $propertyOne !== null && $propertyTwo !== null
            ? $propertyOne->isEqual($propertyTwo)
            : $propertyOne === null && $propertyTwo === null;
    }

    protected function areEqualDateTimeValues(?\DateTimeImmutable $dtOne, ?\DateTimeImmutable $dtTwo): bool
    {
        return $dtOne !== null && $dtTwo !== null
            ? $dtOne->format(\DateTimeInterface::ATOM) === $dtTwo->format(\DateTimeInterface::ATOM)
            : $dtOne === null && $dtTwo === null;
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
