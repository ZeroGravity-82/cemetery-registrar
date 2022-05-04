<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\ORM;

use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlock;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockCollection;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockName;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Infrastructure\Domain\BurialPlace\GraveSite\Doctrine\ORM\CemeteryBlockRepository as DoctrineOrmCemeteryBlockRepository;
use Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite\CemeteryBlockProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    protected string $entityClassName           = CemeteryBlock::class;
    protected string $entityIdClassName         = CemeteryBlockId::class;
    protected string $entityCollectionClassName = CemeteryBlockCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmCemeteryBlockRepository($this->entityManager);
        $this->entityA = CemeteryBlockProvider::getCemeteryBlockA();
        $this->entityB = CemeteryBlockProvider::getCemeteryBlockB();
        $this->entityC = CemeteryBlockProvider::getCemeteryBlockC();
    }

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var CemeteryBlock $entityOne */
        /** @var CemeteryBlock $entityTwo */
        $isSameClass = $entityOne instanceof CemeteryBlock && $entityTwo instanceof CemeteryBlock;

        // Mandatory properties
        $isSameId   = $entityOne->id()->isEqual($entityTwo->id());
        $isSameName = $entityOne->name()->isEqual($entityTwo->name());

        return
            $isSameClass && $isSameId && $isSameName;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newName = new CemeteryBlockName('общий квартал В');

        /** @var CemeteryBlock $entityA */
        $entityA->setName($newName);
    }
}
