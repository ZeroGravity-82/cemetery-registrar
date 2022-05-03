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
abstract class AbstractRepositoryIntegrationTest extends KernelTestCase
{
    protected Entity                 $entityA;
    protected Entity                 $entityB;
    protected Entity                 $entityC;
    protected EntityManagerInterface $entityManager;
    protected object                 $repo;
    protected string                 $entityClassName;
    protected string                 $entityCollectionClassName;

    abstract protected function doTestItSavesANewEntity(
        Entity $persistedEntity,
        Entity $originEntity,
    ): void;
    abstract protected function doTestItUpdatesAlreadyPersistedEntity(
        Entity $persistedEntity,
        Entity $updatedEntity,
        Entity $originEntity,
    ): void;
    abstract protected function doTestItUpdatesAlreadyPersistedEntityWhenSavesACollection(
        Entity $persistedEntity,
        Entity $updatedEntity,
        Entity $originEntity,
    ): void;
    abstract protected function updateEntity(
        Entity $entity,
    ): void;

    public function testItSavesANewEntity(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->entityA);
        $this->entityManager->clear();

        // Testing itself
        $persistedEntity = $this->repo->findById($this->entityA->id());
        $this->doTestItSavesANewEntity($persistedEntity);
        $this->assertSame(1, $this->getRowCount($this->entityClassName));
        $this->assertSame(
            $this->entityA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedEntity->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->entityA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedEntity->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedEntity->removedAt());
    }

    public function testItUpdatesAlreadyPersistedEntity(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->entityA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntity = $this->repo->findById($this->entityA->id());
        $this->assertInstanceOf($this->entityClassName, $persistedEntity);
        $this->updateEntity($persistedEntity);
        $updatedEntity = $persistedEntity;
        sleep(1);                                   // for correct updatedAt timestamp
        $this->repo->save($persistedEntity);
        $this->entityManager->clear();

        $persistedEntity = $this->repo->findById($this->entityA->id());
        $this->doTestItUpdatesAlreadyPersistedEntity($persistedEntity, $updatedEntity, $this->entityA);
        $this->assertSame(1, $this->getRowCount($this->entityClassName));
        $this->assertSame(
            $this->entityA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedEntity->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->entityA->updatedAt() < $persistedEntity->updatedAt());
        $this->assertNull($persistedEntity->removedAt());
    }

    public function testItSavesACollectionOfNewEntities(): void
    {
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->entityA->id()));
        $this->assertNotNull($this->repo->findById($this->entityB->id()));
        $this->assertNotNull($this->repo->findById($this->entityC->id()));
        $this->assertSame(3, $this->getRowCount($this->entityClassName));
    }

    public function testItUpdatesAlreadyPersistedEntityWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount($this->entityClassName));

        // Testing itself
        $persistedEntity = $this->repo->findById($this->entityA->id());
        $this->assertInstanceOf($this->entityClassName, $persistedEntity);
        $this->updateEntity($persistedEntity);
        $updatedEntity = $persistedEntity;
        sleep(1);                                   // for correct updatedAt timestamp
        $this->repo->saveAll(new $this->entityCollectionClassName([$persistedEntity, $this->entityC]));
        $this->entityManager->clear();

        $persistedEntity = $this->repo->findById($this->entityA->id());


        // doTestEqualsEntities()


    }



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
