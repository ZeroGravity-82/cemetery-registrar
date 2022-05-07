<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\Orm\DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\RepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepositoryIntegrationTest extends RepositoryIntegrationTest
{
    protected string $entityClassName           = Burial::class;
    protected string $entityIdClassName         = BurialId::class;
    protected string $entityCollectionClassName = BurialCollection::class;

    private Burial $entityD;
    private Burial $entityE;
    private Burial $entityF;

    public function setUp(): void
    {
        parent::setUp();

        $this->repo    = new DoctrineOrmBurialRepository($this->entityManager);
        $this->entityA = BurialProvider::getBurialA();
        $this->entityB = BurialProvider::getBurialB();
        $this->entityC = BurialProvider::getBurialC();
        $this->entityD = BurialProvider::getBurialD();
        $this->entityE = BurialProvider::getBurialE();
        $this->entityF = BurialProvider::getBurialF();
    }

    public function testItCountsBurialsByFuneralCompanyId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection(
            [$this->entityA, $this->entityB, $this->entityC, $this->entityD, $this->entityE, $this->entityF]
        ));
        $this->entityManager->clear();
        $this->assertSame(6, $this->getRowCount(Burial::class));

        // Testing itself
        $knownFuneralCompanyId = new FuneralCompanyId(new JuristicPersonId('ID001'));
        $burialCount           = $this->repo->countByFuneralCompanyId($knownFuneralCompanyId);
        $this->assertSame(2, $burialCount);

        $unknownFuneralCompanyId = new FuneralCompanyId(new SoleProprietorId('unknown_id'));
        $burialCount             = $this->repo->countByFuneralCompanyId($unknownFuneralCompanyId);
        $this->assertSame(0, $burialCount);
    }

    public function testItCountsBurialsByCustomerId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection(
            [$this->entityA, $this->entityB, $this->entityC, $this->entityD, $this->entityE, $this->entityF]
        ));
        $this->entityManager->clear();
        $this->assertSame(6, $this->getRowCount(Burial::class));

        // Testing itself
        $knownCustomerId = new CustomerId(new NaturalPersonId('ID001'));
        $burialCount     = $this->repo->countByCustomerId($knownCustomerId);
        $this->assertSame(3, $burialCount);

        $unknownCustomerId = new CustomerId(new SoleProprietorId('unknown_id'));
        $burialCount       = $this->repo->countByCustomerId($unknownCustomerId);
        $this->assertSame(0, $burialCount);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var Burial $entityOne */
        /** @var Burial $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->code(), $entityTwo->code()) &&
            $this->areEqualValueObjects($entityOne->deceasedId(), $entityTwo->deceasedId()) &&
            $this->areEqualValueObjects($entityOne->burialType(), $entityTwo->burialType()) &&
            $this->areEqualValueObjects($entityOne->customerId(), $entityTwo->customerId()) &&
            $this->areEqualValueObjects($entityOne->burialPlaceId(), $entityTwo->burialPlaceId()) &&
            $this->areEqualValueObjects($entityOne->burialPlaceOwnerId(), $entityTwo->burialPlaceOwnerId()) &&
            $this->areEqualValueObjects($entityOne->funeralCompanyId(), $entityTwo->funeralCompanyId()) &&
            $this->areEqualValueObjects($entityOne->burialContainer(), $entityTwo->burialContainer()) &&
            $this->areEqualDateTimeValues($entityOne->buriedAt(), $entityTwo->buriedAt());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newCustomerId = new CustomerId(new SoleProprietorId('SP001'));

        /** @var Burial $entityA */
        $entityA->setCustomerId($newCustomerId);
    }
}