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
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRepositoryIntegrationTest extends RepositoryIntegrationTest
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

    protected function checkAreEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var CemeteryBlock $entityOne */
        /** @var CemeteryBlock $entityTwo */
        return
            $this->checkAreSameClasses($entityOne, $entityTwo) &&
            $this->checkAreEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->checkAreEqualValueObjects($entityOne->name(), $entityTwo->name());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newName = new CemeteryBlockName('общий квартал В');

        /** @var CemeteryBlock $entityA */
        $entityA->setName($newName);
    }
}
