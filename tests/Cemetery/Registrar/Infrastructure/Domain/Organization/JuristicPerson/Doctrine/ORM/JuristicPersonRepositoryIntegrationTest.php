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
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var JuristicPerson $entityOne */
        /** @var JuristicPerson $entityTwo */
        $isSameClass = $entityOne instanceof JuristicPerson && $entityTwo instanceof JuristicPerson;

        // Mandatory properties
        $isSameId   = $entityOne->id()->isEqual($entityTwo->id());
        $isSameName = $entityOne->name()->isEqual($entityTwo->name());

        // Optional properties
        $isSameInn = $entityOne->inn() !== null && $entityTwo->inn() !== null
            ? $entityOne->inn()->isEqual($entityTwo->inn())
            : $entityOne->inn() === null && $entityTwo->inn() === null;
        $isSameKpp = $entityOne->kpp() !== null && $entityTwo->kpp() !== null
            ? $entityOne->kpp()->isEqual($entityTwo->kpp())
            : $entityOne->kpp() === null && $entityTwo->kpp() === null;
        $isSameOgrn = $entityOne->ogrn() !== null && $entityTwo->ogrn() !== null
            ? $entityOne->ogrn()->isEqual($entityTwo->ogrn())
            : $entityOne->ogrn() === null && $entityTwo->ogrn() === null;
        $isSameOkpo = $entityOne->okpo() !== null && $entityTwo->okpo() !== null
            ? $entityOne->okpo()->isEqual($entityTwo->okpo())
            : $entityOne->okpo() === null && $entityTwo->okpo() === null;
        $isSameOkved = $entityOne->okved() !== null && $entityTwo->okved() !== null
            ? $entityOne->okved()->isEqual($entityTwo->okved())
            : $entityOne->okved() === null && $entityTwo->okved() === null;
        $isSameLegalAddress = $entityOne->legalAddress() !== null && $entityTwo->legalAddress() !== null
            ? $entityOne->legalAddress()->isEqual($entityTwo->legalAddress())
            : $entityOne->legalAddress() === null && $entityTwo->legalAddress() === null;
        $isSamePostalAddress = $entityOne->postalAddress() !== null && $entityTwo->postalAddress() !== null
            ? $entityOne->postalAddress()->isEqual($entityTwo->postalAddress())
            : $entityOne->postalAddress() === null && $entityTwo->postalAddress() === null;
        $isSameBankDetails = $entityOne->bankDetails() !== null && $entityTwo->bankDetails() !== null
            ? $entityOne->bankDetails()->isEqual($entityTwo->bankDetails())
            : $entityOne->bankDetails() === null && $entityTwo->bankDetails() === null;
        $isSamePhone = $entityOne->phone() !== null && $entityTwo->phone() !== null
            ? $entityOne->phone()->isEqual($entityTwo->phone())
            : $entityOne->phone() === null && $entityTwo->phone() === null;
        $isSamePhoneAdditional = $entityOne->phoneAdditional() !== null && $entityTwo->phoneAdditional() !== null
            ? $entityOne->phoneAdditional()->isEqual($entityTwo->phoneAdditional())
            : $entityOne->phoneAdditional() === null && $entityTwo->phoneAdditional() === null;
        $isSameFax = $entityOne->fax() !== null && $entityTwo->fax() !== null
            ? $entityOne->fax()->isEqual($entityTwo->fax())
            : $entityOne->fax() === null && $entityTwo->fax() === null;
        $isSameGeneralDirector = $entityOne->generalDirector() !== null && $entityTwo->generalDirector() !== null
            ? $entityOne->generalDirector()->isEqual($entityTwo->generalDirector())
            : $entityOne->generalDirector() === null && $entityTwo->generalDirector() === null;
        $isSameEmail = $entityOne->email() !== null && $entityTwo->email() !== null
            ? $entityOne->email()->isEqual($entityTwo->email())
            : $entityOne->email() === null && $entityTwo->email() === null;
        $isSameWebsite= $entityOne->website() !== null && $entityTwo->website() !== null
            ? $entityOne->website()->isEqual($entityTwo->website())
            : $entityOne->website() === null && $entityTwo->website() === null;

        return
            $isSameClass && $isSameId && $isSameName && $isSameInn && $isSameKpp && $isSameOgrn && $isSameOkpo &&
            $isSameOkved && $isSameLegalAddress && $isSamePostalAddress && $isSameBankDetails && $isSamePhone &&
            $isSamePhoneAdditional && $isSameFax && $isSameGeneralDirector && $isSameEmail && $isSameWebsite;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newInn = new Inn('7728168971');

        /** @var JuristicPerson $entityA */
        $entityA->setInn($newInn);
    }
}
