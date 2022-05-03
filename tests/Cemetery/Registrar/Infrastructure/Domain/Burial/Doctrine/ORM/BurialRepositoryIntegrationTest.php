<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCode;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialType;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialContainer\Urn;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Entity;
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
    protected string $entityClassName           = Burial::class;
    protected string $entityCollectionClassName = BurialCollection::class;

    private Burial $entityD;
    private Burial $entityE;
    private Burial $entityF;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->entityA = BurialProvider::getBurialA();
        $this->entityB = BurialProvider::getBurialB();
        $this->entityC = BurialProvider::getBurialC();
        $this->entityD = BurialProvider::getBurialD();
        $this->entityE = BurialProvider::getBurialE();
        $this->entityF = BurialProvider::getBurialF();
        /** @var EntityManagerInterface $entityManager */
        $entityManager       = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;
        $this->repo          = new DoctrineOrmBurialRepository($this->entityManager);
        $this->truncateEntities();
    }

    protected function doTestItSavesANewEntity(
        Entity $persistedEntity,
        Entity $originEntity,
    ): void {
        /** @var Burial $persistedEntity */
        /** @var Burial $originEntity */
        $this->assertInstanceOf(Burial::class, $persistedEntity);
        $this->assertInstanceOf(BurialId::class, $originEntity->id());
        $this->assertTrue($persistedEntity->id()->isEqual($originEntity->id()));
        $this->assertInstanceOf(BurialCode::class, $originEntity->code());
        $this->assertTrue($persistedEntity->code()->isEqual($originEntity->code()));
        $this->assertInstanceOf(DeceasedId::class, $originEntity->deceasedId());
        $this->assertTrue($persistedEntity->deceasedId()->isEqual($originEntity->deceasedId()));
        $this->assertInstanceOf(BurialType::class, $persistedEntity->burialType());
        $this->assertTrue($persistedEntity->burialType()->isEqual($originEntity->burialType()));
        $this->assertInstanceOf(CustomerId::class, $persistedEntity->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedEntity->customerId()->id());
        $this->assertTrue($persistedEntity->customerId()->id()->isEqual($originEntity->customerId()->id()));
        $this->assertInstanceOf(BurialPlaceId::class, $persistedEntity->burialPlaceId());
        $this->assertInstanceOf(ColumbariumNicheId::class, $persistedEntity->burialPlaceId()->id());
        $this->assertTrue($persistedEntity->burialPlaceId()->id()->isEqual($originEntity->burialPlaceId()->id()));
        $this->assertNull($persistedEntity->burialPlaceOwnerId());
        $this->assertNull($persistedEntity->funeralCompanyId());
        $this->assertInstanceOf(BurialContainer::class, $persistedEntity->burialContainer());
        $this->assertInstanceOf(Urn::class, $persistedEntity->burialContainer()->container());
        $this->assertInstanceOf(\DateTimeImmutable::class, $persistedEntity->buriedAt());
        $this->assertSame(
            $originEntity->buriedAt()->format(\DateTimeInterface::ATOM),
            $persistedEntity->buriedAt()->format(\DateTimeInterface::ATOM)
        );
    }

    protected function updateEntity(Entity $entity): void
    {
        /** @var Burial $entity */
        $entity->setBuriedAt(null);
        $entity->setBurialPlaceOwnerId(new NaturalPersonId('NP030'));
    }

    protected function doTestItUpdatesAlreadyPersistedEntity(
        Entity $persistedEntity,
        Entity $updatedEntity,
        Entity $originEntity,
    ): void {
        /** @var Burial $persistedEntity */
        /** @var Burial $updatedEntity */
        /** @var Burial $originEntity */
        $this->assertInstanceOf(Burial::class, $persistedEntity);
        $this->assertNull($persistedEntity->buriedAt());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedEntity->burialPlaceOwnerId());
        $this->assertTrue($persistedEntity->burialPlaceOwnerId()->isEqual($updatedEntity->burialPlaceOwnerId()));
        $this->assertTrue($originEntity->updatedAt() < $persistedEntity->updatedAt());
    }

    protected function doTestItUpdatesAlreadyPersistedEntityWhenSavesACollection(
        Entity $persistedEntity,
        Entity $updatedEntity,
        Entity $originEntity,
    ): void {
        /** @var Burial $persistedEntity */
        /** @var Burial $updatedEntity */
        /** @var Burial $originEntity */

        $this->assertInstanceOf(Burial::class, $persistedEntity);
        $this->assertNull($persistedEntity->buriedAt());
        $this->assertTrue($persistedEntity->burialPlaceOwnerId()->isEqual($newBurialPlaceOwnerId));
        $this->assertTrue($originEntity->updatedAt() < $persistedEntity->updatedAt());

        $persistedEntity = $this->repo->findById($this->entityB->id());
        $this->assertInstanceOf(Burial::class, $persistedEntity);
        $this->assertTrue($persistedEntity->id()->isEqual($this->entityB->id()));
        $this->assertTrue($persistedEntity->code()->isEqual($this->entityB->code()));
        $this->assertTrue($persistedEntity->deceasedId()->isEqual($this->entityB->deceasedId()));
        $this->assertInstanceOf(BurialType::class, $persistedEntity->burialType());
        $this->assertTrue($persistedEntity->burialType()->isCoffinInGraveSite());
        $this->assertInstanceOf(CustomerId::class, $persistedEntity->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedEntity->customerId()->id());
        $this->assertTrue($persistedEntity->customerId()->id()->isEqual($this->entityB->customerId()->id()));
        $this->assertInstanceOf(BurialPlaceId::class, $persistedEntity->burialPlaceId());
        $this->assertInstanceOf(GraveSiteId::class, $persistedEntity->burialPlaceId()->id());
        $this->assertTrue($persistedEntity->burialPlaceId()->id()->isEqual($this->entityB->burialPlaceId()->id()));
        $this->assertInstanceOf(NaturalPersonId::class, $persistedEntity->burialPlaceOwnerId());
        $this->assertTrue($persistedEntity->burialPlaceOwnerId()->isEqual($this->entityB->burialPlaceOwnerId()));
        $this->assertNull($persistedEntity->funeralCompanyId());
        $this->assertInstanceOf(BurialContainer::class, $persistedEntity->burialContainer());
        $this->assertTrue($persistedEntity->burialContainer()->isEqual($this->entityB->burialContainer()));
        $this->assertNull($persistedEntity->buriedAt());

        $persistedEntity = $this->repo->findById($this->entityC->id());
        $this->assertInstanceOf(Burial::class, $persistedEntity);
        $this->assertTrue($persistedEntity->id()->isEqual($this->entityC->id()));
        $this->assertTrue($persistedEntity->code()->isEqual($this->entityC->code()));
        $this->assertTrue($persistedEntity->deceasedId()->isEqual($this->entityC->deceasedId()));
        $this->assertInstanceOf(BurialType::class, $persistedEntity->burialType());
        $this->assertTrue($persistedEntity->burialType()->isAshesUnderMemorialTree());
        $this->assertInstanceOf(CustomerId::class, $persistedEntity->customerId());
        $this->assertInstanceOf(NaturalPersonId::class, $persistedEntity->customerId()->id());
        $this->assertTrue($persistedEntity->customerId()->id()->isEqual($this->entityC->customerId()->id()));
        $this->assertInstanceOf(BurialPlaceId::class, $persistedEntity->burialPlaceId());
        $this->assertInstanceOf(MemorialTreeId::class, $persistedEntity->burialPlaceId()->id());
        $this->assertTrue($persistedEntity->burialPlaceId()->id()->isEqual($this->entityC->burialPlaceId()->id()));
        $this->assertTrue($persistedEntity->burialPlaceOwnerId()->isEqual($this->entityC->burialPlaceOwnerId()));
        $this->assertInstanceOf(JuristicPersonId::class, $persistedEntity->funeralCompanyId()->id());
        $this->assertTrue($persistedEntity->funeralCompanyId()->isEqual($this->entityC->funeralCompanyId()));
        $this->assertNull($persistedEntity->burialContainer());
        $this->assertNull($persistedEntity->buriedAt());

        $this->assertSame(3, $this->getRowCount(Burial::class));
    }

    public function testItRemovesABurial(): void
    {
        // Prepare the repo for testing
        $this->repo->save($this->entityA);
        $this->entityManager->clear();
        $this->assertSame(1, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurial = $this->repo->findById($this->entityA->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->repo->remove($persistedBurial);
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->entityA->id()));
        $this->assertSame(1, $this->getRowCount(Burial::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, $this->entityA->id()->value()));
    }

    public function testItRemovesACollectionOfBurials(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();
        $this->assertSame(3, $this->getRowCount(Burial::class));

        // Testing itself
        $persistedBurialB = $this->repo->findById($this->entityB->id());
        $persistedBurialC = $this->repo->findById($this->entityC->id());
        $this->assertInstanceOf(Burial::class, $persistedBurialB);
        $this->assertInstanceOf(Burial::class, $persistedBurialC);
        $this->repo->removeAll(new BurialCollection([$persistedBurialB, $persistedBurialC]));
        $this->entityManager->clear();

        $this->assertNull($this->repo->findById($this->entityB->id()));
        $this->assertNull($this->repo->findById($this->entityC->id()));
        $this->assertNotNull($this->repo->findById($this->entityA->id()));
        $this->assertSame(3, $this->getRowCount(Burial::class));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, $this->entityB->id()->value()));
        $this->assertNotNull($this->getRemovedAtTimestampById(Burial::class, $this->entityC->id()->value()));
        $this->assertNull($this->getRemovedAtTimestampById(Burial::class, $this->entityA->id()->value()));
    }

    public function testItFindsABurialById(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new BurialCollection([$this->entityA, $this->entityB, $this->entityC]));
        $this->entityManager->clear();

        // Testing itself
        $persistedBurial = $this->repo->findById($this->entityB->id());
        $this->assertInstanceOf(Burial::class, $persistedBurial);
        $this->assertTrue($persistedBurial->id()->isEqual($this->entityB->id()));
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
}
