<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM;

use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Domain\Organization\JuristicPerson\Doctrine\ORM\JuristicPersonRepository as DoctrineORMJuristicPersonRepository;
use Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson\JuristicPersonProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRepositoryIntegrationTest extends RepositoryIntegrationTest
{
    protected string $entityClassName           = JuristicPerson::class;
    protected string $entityIdClassName         = JuristicPersonId::class;
    protected string $entityCollectionClassName = JuristicPersonCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineORMJuristicPersonRepository($this->entityManager);
        $this->entityA = JuristicPersonProvider::getJuristicPersonA();
        $this->entityB = JuristicPersonProvider::getJuristicPersonB();
        $this->entityC = JuristicPersonProvider::getJuristicPersonC();
    }

    public function testItHydratesBankDetailsEmbeddable(): void
    {
        $this->repo->saveAll(new JuristicPersonCollection([$this->entityA, $this->entityB]));
        $this->entityManager->clear();

        $persistedEntityA = $this->repo->findById($this->entityA->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedEntityA);
        $this->assertNull($persistedEntityA->bankDetails());

        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $this->assertInstanceOf(JuristicPerson::class, $persistedEntityB);
        $this->assertInstanceOf(BankDetails::class, $persistedEntityB->bankDetails());
        $this->assertTrue($persistedEntityB->bankDetails()->isEqual($this->entityB->bankDetails()));
    }

    protected function checkAreEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var JuristicPerson $entityOne */
        /** @var JuristicPerson $entityTwo */
        return
            $this->checkAreSameClasses($entityOne, $entityTwo) &&
            $this->checkAreEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->checkAreEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->checkAreEqualValueObjects($entityOne->inn(), $entityTwo->inn()) &&
            $this->checkAreEqualValueObjects($entityOne->kpp(), $entityTwo->kpp()) &&
            $this->checkAreEqualValueObjects($entityOne->ogrn(), $entityTwo->ogrn()) &&
            $this->checkAreEqualValueObjects($entityOne->okpo(), $entityTwo->okpo()) &&
            $this->checkAreEqualValueObjects($entityOne->okved(), $entityTwo->okved()) &&
            $this->checkAreEqualValueObjects($entityOne->legalAddress(), $entityTwo->legalAddress()) &&
            $this->checkAreEqualValueObjects($entityOne->postalAddress(), $entityTwo->postalAddress()) &&
            $this->checkAreEqualValueObjects($entityOne->bankDetails(), $entityTwo->bankDetails()) &&
            $this->checkAreEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->checkAreEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->checkAreEqualValueObjects($entityOne->fax(), $entityTwo->fax()) &&
            $this->checkAreEqualValueObjects($entityOne->generalDirector(), $entityTwo->generalDirector()) &&
            $this->checkAreEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->checkAreEqualValueObjects($entityOne->website(), $entityTwo->website());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newInn = new Inn('7728168971');

        /** @var JuristicPerson $entityA */
        $entityA->setInn($newInn);
    }
}
