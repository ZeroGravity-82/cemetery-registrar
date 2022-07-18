<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\Burial\Burial;
use Cemetery\Registrar\Domain\Model\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Model\Burial\BurialId;
use Cemetery\Registrar\Domain\Model\Burial\BurialRepositoryValidator;
use Cemetery\Registrar\Domain\Model\Burial\CustomerId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmBurialRepository;
use DataFixtures\Burial\BurialProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmBurialRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = Burial::class;
    protected string $entityIdClassName         = BurialId::class;
    protected string $entityCollectionClassName = BurialCollection::class;

    private Burial $entityD;
    private Burial $entityE;
    private Burial $entityF;
    private Burial $entityG;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepositoryValidator = $this->createMock(BurialRepositoryValidator::class);
        $this->repo                    = new DoctrineOrmBurialRepository(
            $this->entityManager,
            $this->mockRepositoryValidator,
        );
        $this->entityA = BurialProvider::getBurialA();
        $this->entityB = BurialProvider::getBurialB();
        $this->entityC = BurialProvider::getBurialC();
        $this->entityD = BurialProvider::getBurialD();
        $this->entityE = BurialProvider::getBurialE();
        $this->entityF = BurialProvider::getBurialF();
        $this->entityG = BurialProvider::getBurialG();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(Burial::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(BurialId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(BurialCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    public function testItCountsBurialsByFuneralCompanyId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownFuneralCompanyId = new FuneralCompanyId('FC001');
        $burialCount           = $this->repo->countByFuneralCompanyId($knownFuneralCompanyId);
        $this->assertSame(2, $burialCount);

        $knownFuneralCompanyId = new FuneralCompanyId('FC003');
        $burialCount           = $this->repo->countByFuneralCompanyId($knownFuneralCompanyId);
        $this->assertSame(1, $burialCount);

        $unknownFuneralCompanyId = new FuneralCompanyId('unknown_id');
        $burialCount             = $this->repo->countByFuneralCompanyId($unknownFuneralCompanyId);
        $this->assertSame(0, $burialCount);
    }

    public function testItDoesNotCountRemovedBurialsByFuneralCompanyId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityF  = $this->repo->findById($this->entityF->id());
        $funeralCompanyIdF = $persistedEntityF->funeralCompanyId();
        $this->repo->remove($persistedEntityF);
        $this->entityManager->clear();

        $burialCount = $this->repo->countByFuneralCompanyId($funeralCompanyIdF);
        $this->assertSame(0, $burialCount);
    }

    public function testItCountsBurialsByCustomerId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownCustomerId = new CustomerId(new NaturalPersonId('NP005'));
        $burialCount     = $this->repo->countByCustomerId($knownCustomerId);
        $this->assertSame(2, $burialCount);

        $unknownCustomerId = new CustomerId(new SoleProprietorId('unknown_id'));
        $burialCount       = $this->repo->countByCustomerId($unknownCustomerId);
        $this->assertSame(0, $burialCount);
    }

    public function testItDoesNotCountRemovedBurialsByCustomerId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $customerIdB      = $persistedEntityB->customerId();
        $this->repo->remove($persistedEntityB);
        $this->entityManager->clear();

        $burialCount = $this->repo->countByCustomerId($customerIdB);
        $this->assertSame(1, $burialCount);
    }

    public function testItCountsBurialsByGraveSiteId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownGraveSiteId = new GraveSiteId('GS005');
        $burialCount      = $this->repo->countByGraveSiteId($knownGraveSiteId);
        $this->assertSame(1, $burialCount);

        $unknownGraveSiteId = new GraveSiteId('unknown_id');
        $burialCount        = $this->repo->countByGraveSiteId($unknownGraveSiteId);
        $this->assertSame(0, $burialCount);
    }

    public function testItDoesNotCountRemovedBurialsByGraveSiteId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityG = $this->repo->findById($this->entityG->id());
        $graveSiteIdG     = $persistedEntityG->burialPlaceId()->id();
        $this->repo->remove($persistedEntityG);
        $this->entityManager->clear();

        $burialCount = $this->repo->countByGraveSiteId($graveSiteIdG);
        $this->assertSame(0, $burialCount);
    }

    public function testItCountsBurialsByColumbariumNicheId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownColumbariumNicheId = new ColumbariumNicheId('CN002');
        $burialCount             = $this->repo->countByColumbariumNicheId($knownColumbariumNicheId);
        $this->assertSame(1, $burialCount);

        $unknownColumbariumNicheId = new ColumbariumNicheId('unknown_id');
        $burialCount               = $this->repo->countByColumbariumNicheId($unknownColumbariumNicheId);
        $this->assertSame(0, $burialCount);
    }

    public function testItDoesNotCountRemovedBurialsByColumbariumNicheId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityA    = $this->repo->findById($this->entityA->id());
        $columbariumNicheIdA = $persistedEntityA->burialPlaceId()->id();
        $this->repo->remove($persistedEntityA);
        $this->entityManager->clear();

        $burialCount = $this->repo->countByColumbariumNicheId($columbariumNicheIdA);
        $this->assertSame(0, $burialCount);
    }

    public function testItCountsBurialsByMemorialTreeId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownMemorialTreeId = new MemorialTreeId('MT002');
        $burialCount         = $this->repo->countByMemorialTreeId($knownMemorialTreeId);
        $this->assertSame(1, $burialCount);

        $unknownMemorialTreeId = new MemorialTreeId('unknown_id');
        $burialCount           = $this->repo->countByMemorialTreeId($unknownMemorialTreeId);
        $this->assertSame(0, $burialCount);
    }

    public function testItDoesNotCountRemovedBurialsByMemorialTreeId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
        ]));
        $this->entityManager->clear();

        // Testing itself
        $persistedEntityC = $this->repo->findById($this->entityC->id());
        $memorialTreeIdC  = $persistedEntityC->burialPlaceId()->id();
        $this->repo->remove($persistedEntityC);
        $this->entityManager->clear();

        $burialCount = $this->repo->countByMemorialTreeId($memorialTreeIdC);
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
            $this->areEqualValueObjects($entityOne->type(), $entityTwo->type()) &&
            $this->areEqualValueObjects($entityOne->customerId(), $entityTwo->customerId()) &&
            $this->areEqualValueObjects($entityOne->burialPlaceId(), $entityTwo->burialPlaceId()) &&
            $this->areEqualValueObjects($entityOne->personInChargeId(), $entityTwo->personInChargeId()) &&
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
