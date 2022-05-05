<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\ORM;

use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\Inn;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorCollection;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Domain\Organization\SoleProprietor\Doctrine\ORM\SoleProprietorRepository as DoctrineORMSoleProprietorRepository;
use Cemetery\Tests\Registrar\Domain\Organization\SoleProprietor\SoleProprietorProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRepositoryIntegrationTest extends RepositoryIntegrationTest
{
    protected string $entityClassName           = SoleProprietor::class;
    protected string $entityIdClassName         = SoleProprietorId::class;
    protected string $entityCollectionClassName = SoleProprietorCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineORMSoleProprietorRepository($this->entityManager);
        $this->entityA = SoleProprietorProvider::getSoleProprietorA();
        $this->entityB = SoleProprietorProvider::getSoleProprietorB();
        $this->entityC = SoleProprietorProvider::getSoleProprietorC();
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new SoleProprietorCollection([$this->entityA, $this->entityB]));
        $this->entityManager->clear();

        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedEntityA);
        $this->assertNull($persistedEntityA->bankDetails());

        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $this->assertInstanceOf(SoleProprietor::class, $persistedEntityB);
        $this->assertInstanceOf(BankDetails::class, $persistedEntityB->bankDetails());
        $this->assertTrue($persistedEntityB->bankDetails()->isEqual($this->entityB->bankDetails()));
    }

    protected function checkAreEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var SoleProprietor $entityOne */
        /** @var SoleProprietor $entityTwo */
        return
            $this->checkAreSameClasses($entityOne, $entityTwo) &&
            $this->checkAreEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->checkAreEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->checkAreEqualValueObjects($entityOne->inn(), $entityTwo->inn()) &&
            $this->checkAreEqualValueObjects($entityOne->ogrnip(), $entityTwo->ogrnip()) &&
            $this->checkAreEqualValueObjects($entityOne->okpo(), $entityTwo->okpo()) &&
            $this->checkAreEqualValueObjects($entityOne->okved(), $entityTwo->okved()) &&
            $this->checkAreEqualValueObjects($entityOne->registrationAddress(), $entityTwo->registrationAddress()) &&
            $this->checkAreEqualValueObjects($entityOne->actualLocationAddress(), $entityTwo->actualLocationAddress()) &&
            $this->checkAreEqualValueObjects($entityOne->bankDetails(), $entityTwo->bankDetails()) &&
            $this->checkAreEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->checkAreEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->checkAreEqualValueObjects($entityOne->fax(), $entityTwo->fax()) &&
            $this->checkAreEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->checkAreEqualValueObjects($entityOne->website(), $entityTwo->website());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newInn = new Inn('772208786091');

        /** @var SoleProprietor $entityA */
        $entityA->setInn($newInn);
    }
}
