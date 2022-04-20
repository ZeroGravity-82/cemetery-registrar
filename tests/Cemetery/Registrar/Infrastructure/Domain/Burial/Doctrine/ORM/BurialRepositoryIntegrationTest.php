<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM\BurialRepository as DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
{
    private Burial                      $burialA;
    private Burial                      $burialB;
    private Burial                      $burialC;
    private Burial                      $burialD;
    private Burial                      $burialE;
    private Burial                      $burialF;
    private DoctrineOrmBurialRepository $repo;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->burialA = BurialProvider::getBurialA();
        $this->burialB = BurialProvider::getBurialB();
        $this->burialC = BurialProvider::getBurialC();
        $this->burialD = BurialProvider::getBurialD();
        $this->burialE = BurialProvider::getBurialE();
        $this->burialF = BurialProvider::getBurialF();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineOrmBurialRepository(
            $this->entityManager,
        );
        $this->truncateEntities();
    }

    public function testItSavesANewBurial(): void
    {
        $this->repo->save($this->burialA);
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B001', $persistedBurial->id()->value());
        $this->assertSame('BC001', $persistedBurial->code()->value());
        $this->assertSame('D001', $persistedBurial->deceasedId()->value());
        $this->assertInstanceOf(BurialType::class, $persistedBurial->burialType());
        $this->assertTrue($persistedBurial->burialType()->isUrnInColumbariumNiche());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->customerId()->id());
        $this->assertSame('ID001', $persistedBurial->customerId()->id()->value());
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->burialPlaceId());
        $this->assertInstanceOf(ColumbariumNicheId::class, $persistedBurial->burialPlaceId()->id());
        $this->assertSame('CN001', $persistedBurial->burialPlaceId()->id()->value());
        $this->assertNull($persistedBurial->burialPlaceOwnerId());
        $this->assertNull($persistedBurial->funeralCompanyId());
        $this->assertInstanceOf(BurialContainer::class, $persistedBurial->burialContainer());
        $this->assertInstanceOf(Urn::class, $persistedBurial->burialContainer()->container());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedBurial->buriedAt());
        $this->assertSame('2022-01-15 13:10:00', $persistedBurial->buriedAt()->format('Y-m-d H:i:s'));

        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertSame(
            $this->burialA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedBurial->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertSame(
            $this->burialA->updatedAt()->format(\DateTimeInterface::ATOM),
            $persistedBurial->updatedAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertNull($persistedBurial->removedAt());
    }

    public function testItUpdatesAnExistingBurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $persistedBurial->setBuriedAt(null);
        $persistedBurial->setBurialPlaceOwnerId(new NaturalPersonId('ID003'));
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->save($persistedBurial);
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertNull($persistedBurial->buriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->burialPlaceOwnerId());
        $this->assertSame('ID003', (string) $persistedBurial->burialPlaceOwnerId());
        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertSame(
            $this->burialA->createdAt()->format(\DateTimeInterface::ATOM),
            $persistedBurial->createdAt()->format(\DateTimeInterface::ATOM)
        );
        $this->assertTrue($this->burialA->updatedAt() < $persistedBurial->updatedAt());
        $this->assertNull($persistedBurial->removedAt());
    }

    public function testItSavesACollectionOfNewBurials(): void
    {
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();

        $this->assertNotNull($this->repo->findById($this->burialA->id()));
        $this->assertNotNull($this->repo->findById($this->burialB->id()));
        $this->assertNotNull($this->repo->findById($this->burialC->id()));
        $this->assertSame(3, $this->getRowCount(Burial::class));
    }

    public function testItUpdatesExistingBurialsWhenSavesACollection(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB]));
        $this->entityManager->clear();
        $this->assertSame(2, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $persistedBurial->setBuriedAt(null);
        $persistedBurial->setBurialPlaceOwnerId(new NaturalPersonId('NP001'));
        sleep(1);   // for correct updatedAt timestamp
        $this->repo->saveAll(new BurialCollection([$persistedBurial, $this->burialC]));
        $this->entityManager->clear();

        $persistedBurial = $this->repo->findById($this->burialA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertNull($persistedBurial->buriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->burialPlaceOwnerId());
        $this->assertSame('NP001', (string) $persistedBurial->burialPlaceOwnerId());
        $this->assertTrue($this->burialA->updatedAt() < $persistedBurial->updatedAt());

        $persistedBurial = $this->repo->findById($this->burialB->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B002', (string) $persistedBurial->id());
        $this->assertSame('BC002', (string) $persistedBurial->code());
        $this->assertSame('D002', (string) $persistedBurial->deceasedId());
        $this->assertInstanceOf(BurialType::class, $persistedBurial->burialType());
        $this->assertTrue($persistedBurial->burialType()->isCoffinInGraveSite());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->customerId()->id());
        $this->assertSame('ID001', $persistedBurial->customerId()->id()->value());
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->burialPlaceId());
        $this->assertInstanceOf(GraveSiteId::class, $persistedBurial->burialPlaceId()->id());
        $this->assertSame('GS001', $persistedBurial->burialPlaceId()->id()->value());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->burialPlaceOwnerId());
        $this->assertSame('ID001', (string) $persistedBurial->burialPlaceOwnerId());
        $this->assertNull($persistedBurial->funeralCompanyId());
        $this->assertInstanceOf(BurialContainer::class, $persistedBurial->burialContainer());
        $this->assertInstanceOf(Coffin::class, $persistedBurial->burialContainer()->container());
        $this->assertTrue($persistedBurial->burialContainer()->container()->size()->isEqual(new CoffinSize(180)));
        $this->assertTrue($persistedBurial->burialContainer()->container()->shape()->isEqual(CoffinShape::trapezoid()));
        $this->assertFalse($persistedBurial->burialContainer()->container()->isNonStandard());
        $this->assertNull($persistedBurial->buriedAt());

        $persistedBurial = $this->repo->findById($this->burialC->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B003', (string) $persistedBurial->id());
        $this->assertSame('BC003', (string) $persistedBurial->code());
        $this->assertSame('D003', (string) $persistedBurial->deceasedId());
        $this->assertInstanceOf(BurialType::class, $persistedBurial->burialType());
        $this->assertTrue($persistedBurial->burialType()->isAshesUnderMemorialTree());
        $this->assertInstanceOf(CustomerId::class, $persistedBurial->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedBurial->customerId()->id());
        $this->assertSame('ID001', $persistedBurial->customerId()->id()->value());
        $this->assertInstanceOf(BurialPlaceId::class, $persistedBurial->burialPlaceId());
        $this->assertInstanceOf(MemorialTreeId::class, $persistedBurial->burialPlaceId()->id());
        $this->assertSame('MT001', $persistedBurial->burialPlaceId()->id()->value());
        $this->assertSame('ID003', (string) $persistedBurial->burialPlaceOwnerId());
        $this->assertInstanceOf(JuristicPersonId::class, $persistedBurial->funeralCompanyId()->id());
        $this->assertSame('ID001', (string) $persistedBurial->funeralCompanyId()->id());
        $this->assertNull($persistedBurial->burialContainer());
        $this->assertNull($persistedBurial->buriedAt());

        $this->assertSame(3, $this->getRowCount(Burial::class));
    }

    public function testItRemovesABurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->burialA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->repo->remove($persistedBurial);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->burialA->id()));
        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialA->id()));
    }

    public function testItRemovesACollectionOfBurials(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurialB = $this->repo->findById($this->burialB->id());
        $persistedBurialC = $this->repo->findById($this->burialC->id());
        $this->assertInstanceOf(Burial::class, $persistedBurialB);
        $this->assertInstanceOf(Burial::class, $persistedBurialC);
        $this->repo->removeAll(new BurialCollection([$persistedBurialB, $persistedBurialC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->burialB->id()));
        $this->assertNull($this->repo->findById($this->burialC->id()));
        $this->assertNotNull($this->repo->findById($this->burialA->id()));
        $this->assertSame(3, $this->getRowCount(Burial::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialB->id()));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialC->id()));
        $this->assertNull($this->getRemovedAtTimestampById(Burial::class, (string) $this->burialA->id()));
    }

    public function testItFindsABurialById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->burialA, $this->burialB, $this->burialC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedBurial = $this->repo->findById($this->burialB->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertSame('B002', (string) $persistedBurial->id());
    }

    public function testItReturnsNullIfABurialIsNotFoundById(): void
    {
        $burial = $this->repo->findById(new BurialId('unknown_id'));
        $this->assertNull($burial);
    }

    public function testItCountsBurialsByFuneralCompanyId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection(
            [$this->burialA, $this->burialB, $this->burialC, $this->burialD, $this->burialE, $this->burialF]
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
            [$this->burialA, $this->burialB, $this->burialC, $this->burialD, $this->burialE, $this->burialF]
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
}
