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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var SoleProprietor $entityOne */
        /** @var SoleProprietor $entityTwo */
        $isSameClass = $entityOne instanceof SoleProprietor && $entityTwo instanceof SoleProprietor;

        // Mandatory properties
        $isSameId   = $entityOne->id()->isEqual($entityTwo->id());
        $isSameName = $entityOne->name()->isEqual($entityTwo->name());

        // Optional properties
        $isSameInn = $entityOne->inn() !== null && $entityTwo->inn() !== null
            ? $entityOne->inn()->isEqual($entityTwo->inn())
            : $entityOne->inn() === null && $entityTwo->inn() === null;
        $isSameOgrnip = $entityOne->ogrnip() !== null && $entityTwo->ogrnip() !== null
            ? $entityOne->ogrnip()->isEqual($entityTwo->ogrnip())
            : $entityOne->ogrnip() === null && $entityTwo->ogrnip() === null;
        $isSameOkpo = $entityOne->okpo() !== null && $entityTwo->okpo() !== null
            ? $entityOne->okpo()->isEqual($entityTwo->okpo())
            : $entityOne->okpo() === null && $entityTwo->okpo() === null;
        $isSameOkved = $entityOne->okved() !== null && $entityTwo->okved() !== null
            ? $entityOne->okved()->isEqual($entityTwo->okved())
            : $entityOne->okved() === null && $entityTwo->okved() === null;
        $isSameRegistrationAddress = $entityOne->registrationAddress() !== null && $entityTwo->registrationAddress() !== null
            ? $entityOne->registrationAddress()->isEqual($entityTwo->registrationAddress())
            : $entityOne->registrationAddress() === null && $entityTwo->registrationAddress() === null;
        $isSameActualLocationAddress = $entityOne->actualLocationAddress() !== null && $entityTwo->actualLocationAddress() !== null
            ? $entityOne->actualLocationAddress()->isEqual($entityTwo->actualLocationAddress())
            : $entityOne->actualLocationAddress() === null && $entityTwo->actualLocationAddress() === null;
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
        $isSameEmail = $entityOne->email() !== null && $entityTwo->email() !== null
            ? $entityOne->email()->isEqual($entityTwo->email())
            : $entityOne->email() === null && $entityTwo->email() === null;
        $isSameWebsite= $entityOne->website() !== null && $entityTwo->website() !== null
            ? $entityOne->website()->isEqual($entityTwo->website())
            : $entityOne->website() === null && $entityTwo->website() === null;

        return
            $isSameClass && $isSameId && $isSameName && $isSameInn && $isSameOgrnip && $isSameOkpo && $isSameOkved &&
            $isSameRegistrationAddress && $isSameActualLocationAddress && $isSameBankDetails && $isSamePhone &&
            $isSamePhoneAdditional && $isSameFax && $isSameEmail && $isSameWebsite;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newInn = new Inn('772208786091');

        /** @var SoleProprietor $entityA */
        $entityA->setInn($newInn);
    }
}
