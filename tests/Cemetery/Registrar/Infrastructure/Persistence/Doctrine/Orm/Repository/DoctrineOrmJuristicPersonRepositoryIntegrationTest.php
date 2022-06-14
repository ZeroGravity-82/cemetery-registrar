<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\Inn;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonCollection;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmJuristicPersonRepository;
use DataFixtures\Organization\JuristicPerson\JuristicPersonProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmJuristicPersonRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = JuristicPerson::class;
    protected string $entityIdClassName         = JuristicPersonId::class;
    protected string $entityCollectionClassName = JuristicPersonCollection::class;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmJuristicPersonRepository($this->entityManager);
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

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var JuristicPerson $entityOne */
        /** @var JuristicPerson $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->name(), $entityTwo->name()) &&
            $this->areEqualValueObjects($entityOne->inn(), $entityTwo->inn()) &&
            $this->areEqualValueObjects($entityOne->kpp(), $entityTwo->kpp()) &&
            $this->areEqualValueObjects($entityOne->ogrn(), $entityTwo->ogrn()) &&
            $this->areEqualValueObjects($entityOne->okpo(), $entityTwo->okpo()) &&
            $this->areEqualValueObjects($entityOne->okved(), $entityTwo->okved()) &&
            $this->areEqualValueObjects($entityOne->legalAddress(), $entityTwo->legalAddress()) &&
            $this->areEqualValueObjects($entityOne->postalAddress(), $entityTwo->postalAddress()) &&
            $this->areEqualValueObjects($entityOne->bankDetails(), $entityTwo->bankDetails()) &&
            $this->areEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->areEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->areEqualValueObjects($entityOne->fax(), $entityTwo->fax()) &&
            $this->areEqualValueObjects($entityOne->generalDirector(), $entityTwo->generalDirector()) &&
            $this->areEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->areEqualValueObjects($entityOne->website(), $entityTwo->website());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newInn = new Inn('7728168971');

        /** @var JuristicPerson $entityA */
        $entityA->setInn($newInn);
    }
}
