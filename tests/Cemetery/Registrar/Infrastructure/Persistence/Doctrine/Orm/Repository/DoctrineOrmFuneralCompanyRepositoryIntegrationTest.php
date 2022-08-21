<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyCollection;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyNote;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmFuneralCompanyRepository;
use DataFixtures\FuneralCompany\FuneralCompanyProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmFuneralCompanyRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = FuneralCompany::class;
    protected string $entityIdClassName         = FuneralCompanyId::class;
    protected string $entityCollectionClassName = FuneralCompanyCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmFuneralCompanyRepository($this->entityManager);
        $this->entityA = FuneralCompanyProvider::getFuneralCompanyA();
        $this->entityB = FuneralCompanyProvider::getFuneralCompanyB();
        $this->entityC = FuneralCompanyProvider::getFuneralCompanyC();
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var FuneralCompany $entityOne */
        /** @var FuneralCompany $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->areEqualValueObjects($entityOne->note(), $entityTwo->note());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newNote = new FuneralCompanyNote('Примечание 1');

        /** @var FuneralCompany $entityA */
        $entityA->setNote($newNote);
    }
}
