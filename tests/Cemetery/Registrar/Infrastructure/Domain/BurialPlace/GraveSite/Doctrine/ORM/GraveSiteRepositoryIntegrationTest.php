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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var GraveSite $entityOne */
        /** @var GraveSite $entityTwo */
        $isSameClass = $entityOne instanceof GraveSite && $entityTwo instanceof GraveSite;

        // Mandatory properties
        $isSameId              = $entityOne->id()->isEqual($entityTwo->id());
        $isSameCemeteryBlockId = $entityOne->cemeteryBlockId()->isEqual($entityTwo->cemeteryBlockId());
        $isSameRowInBlock      = $entityOne->rowInBlock()->isEqual($entityTwo->rowInBlock());

        // Optional properties
        $isSamePositionInRow = $entityOne->positionInRow() !== null && $entityTwo->positionInRow() !== null
            ? $entityOne->positionInRow()->isEqual($entityTwo->positionInRow())
            : $entityOne->positionInRow() === null && $entityTwo->positionInRow() === null;
        $isSameGeoPosition = $entityOne->geoPosition() !== null && $entityTwo->geoPosition() !== null
            ? $entityOne->geoPosition()->isEqual($entityTwo->geoPosition())
            : $entityOne->geoPosition() === null && $entityTwo->geoPosition() === null;
        $isSameSize = $entityOne->size() !== null && $entityTwo->size() !== null
            ? $entityOne->size()->isEqual($entityTwo->size())
            : $entityOne->size() === null && $entityTwo->size() === null;

        return
            $isSameClass && $isSameId && $isSameCemeteryBlockId && $isSameRowInBlock && $isSamePositionInRow &&
            $isSameGeoPosition && $isSameSize;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newCemeteryBlockId = new CemeteryBlockId('CB005');
        $newRowInBlock      = new RowInBlock(15);
        $newPositionInRow   = new PositionInRow(3);
        $newGeoPosition     = new GeoPosition(new Coordinates('54.850357', '81.7972252'), new Error('0.25'));
        $newSize            = new GraveSiteSize('2.0');

        /** @var GraveSite $entityA */
        $entityA->setCemeteryBlockId($newCemeteryBlockId);
        $entityA->setRowInBlock($newRowInBlock);
        $entityA->setPositionInRow($newPositionInRow);
        $entityA->setGeoPosition($newGeoPosition);
        $entityA->setSize($newSize);
    }
}
