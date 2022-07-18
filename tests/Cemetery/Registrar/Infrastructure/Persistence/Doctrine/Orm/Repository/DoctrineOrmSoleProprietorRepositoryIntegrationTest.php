<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorRepositoryValidator;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmSoleProprietorRepository;
use DataFixtures\Organization\SoleProprietor\SoleProprietorProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmSoleProprietorRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = SoleProprietor::class;
    protected string $entityIdClassName         = SoleProprietorId::class;
    protected string $entityCollectionClassName = SoleProprietorCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepositoryValidator = $this->createMock(SoleProprietorRepositoryValidator::class);
        $this->repo                    = new DoctrineOrmSoleProprietorRepository(
            $this->entityManager,
            $this->mockRepositoryValidator,
        );
        $this->entityA = SoleProprietorProvider::getSoleProprietorA();
        $this->entityB = SoleProprietorProvider::getSoleProprietorB();
        $this->entityC = SoleProprietorProvider::getSoleProprietorC();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(SoleProprietor::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(SoleProprietorId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(SoleProprietorCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var SoleProprietor $entityOne */
        /** @var SoleProprietor $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->areEqualValueObjects($entityOne->inn(), $entityTwo->inn()) &&
            $this->areEqualValueObjects($entityOne->ogrnip(), $entityTwo->ogrnip()) &&
            $this->areEqualValueObjects($entityOne->okpo(), $entityTwo->okpo()) &&
            $this->areEqualValueObjects($entityOne->okved(), $entityTwo->okved()) &&
            $this->areEqualValueObjects($entityOne->registrationAddress(), $entityTwo->registrationAddress()) &&
            $this->areEqualValueObjects($entityOne->actualLocationAddress(), $entityTwo->actualLocationAddress()) &&
            $this->areEqualValueObjects($entityOne->bankDetails(), $entityTwo->bankDetails()) &&
            $this->areEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->areEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->areEqualValueObjects($entityOne->fax(), $entityTwo->fax()) &&
            $this->areEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->areEqualValueObjects($entityOne->website(), $entityTwo->website());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newInn = new Inn('772208786091');

        /** @var SoleProprietor $entityA */
        $entityA->setInn($newInn);
    }
}
