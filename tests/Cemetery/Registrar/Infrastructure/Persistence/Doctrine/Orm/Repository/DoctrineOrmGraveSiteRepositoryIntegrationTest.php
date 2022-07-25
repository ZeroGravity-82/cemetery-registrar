<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmGraveSiteRepository;
use DataFixtures\BurialPlace\GraveSite\GraveSiteProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmGraveSiteRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = GraveSite::class;
    protected string $entityIdClassName         = GraveSiteId::class;
    protected string $entityCollectionClassName = GraveSiteCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmGraveSiteRepository($this->entityManager);
        $this->entityA = GraveSiteProvider::getGraveSiteA();
        $this->entityB = GraveSiteProvider::getGraveSiteB();
        $this->entityC = GraveSiteProvider::getGraveSiteC();
    }

    // -------------------------------------- Uniqueness constraints testing ---------------------------------------

    public function testItSavesGraveSiteWithSameRowAndPositionButAnotherCemeteryBlock(): void
    {
        // Prepare the repo for testing
        /** @var GraveSite $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new GraveSite(
            new GraveSiteId('GS00X'),
            new CemeteryBlockId('CB00X'),
            $existingEntity->rowInBlock(),
        ))
            ->setPositionInRow($existingEntity->positionInRow());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItSavesGraveSiteWithSameRowAndPositionAndCemeteryBlockAsRemovedGraveSite(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new $this->entityCollectionClassName([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        /** @var GraveSite $entityToRemove */
        $entityToRemove = $this->repo->findById($this->entityB->id());
        $this->repo->remove($entityToRemove);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new GraveSite(
            new GraveSiteId('GS00X'),
            $entityToRemove->cemeteryBlockId(),
            $entityToRemove->rowInBlock(),
        ))
            ->setPositionInRow($entityToRemove->positionInRow());
        $this->assertNull(
            $this->repo->save($newEntity)
        );
    }

    public function testItFailsToSaveGraveSiteWithSameRowAndPositionAndCemeteryBlock(): void
    {
        // Prepare the repo for testing
        /** @var GraveSite $existingEntity */
        $existingEntity = $this->entityB;
        $this->repo->save($existingEntity);
        $this->entityManager->clear();

        // Testing itself
        $newEntity = (new GraveSite(
            new GraveSiteId('GS00X'),
            $existingEntity->cemeteryBlockId(),
            $existingEntity->rowInBlock(),
        ))
            ->setPositionInRow($existingEntity->positionInRow());
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Участок с такими рядом и местом в этом квартале уже существует.');
        $this->repo->save($newEntity);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var GraveSite $entityOne */
        /** @var GraveSite $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->cemeteryBlockId(), $entityTwo->cemeteryBlockId()) &&
            $this->areEqualValueObjects($entityOne->rowInBlock(), $entityTwo->rowInBlock()) &&
            $this->areEqualValueObjects($entityOne->positionInRow(), $entityTwo->positionInRow()) &&
            $this->areEqualValueObjects($entityOne->geoPosition(), $entityTwo->geoPosition()) &&
            $this->areEqualValueObjects($entityOne->size(), $entityTwo->size());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newSize = new GraveSiteSize('2.0');

        /** @var GraveSite $entityA */
        $entityA->setSize($newSize);
    }
}
