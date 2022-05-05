<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\Error;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\ORM\GraveSiteRepository as DoctrineOrmGraveSiteRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite\GraveSiteProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteRepositoryIntegrationTest extends RepositoryIntegrationTest
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
