<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM;

use Cemetery\Registrar\Domain\Burial\Burial;
use Cemetery\Registrar\Domain\Burial\BurialCollection;
use Cemetery\Registrar\Domain\Burial\BurialId;
use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\Burial\FuneralCompanyId;
use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Domain\Burial\Doctrine\ORM\BurialRepository as DoctrineOrmBurialRepository;
use Cemetery\Tests\Registrar\Domain\Burial\BurialProvider;
use Cemetery\Tests\Registrar\Infrastructure\Domain\AbstractRepositoryIntegrationTest;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialRepositoryIntegrationTest extends AbstractRepositoryIntegrationTest
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

    protected function isEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var Burial $entityOne */
        /** @var Burial $entityTwo */
        $isSameClass = $entityOne instanceof Burial && $entityTwo instanceof Burial;

        // Mandatory properties
        $isSameId         = $entityOne->id()->isEqual($entityTwo->id());
        $isSameCode       = $entityOne->code()->isEqual($entityTwo->code());
        $isSameDeceasedId = $entityOne->deceasedId()->isEqual($entityTwo->deceasedId());
        $isSameBurialType = $entityOne->burialType()->isEqual($entityTwo->burialType());

        // Optional properties
        $isSameCustomerId = $entityOne->customerId() !== null && $entityTwo->customerId() !== null
            ? $entityOne->customerId()->isEqual($entityTwo->customerId())
            : $entityOne->customerId() === null && $entityTwo->customerId() === null;
        $isSameBurialPlaceId = $entityOne->burialPlaceId() !== null && $entityTwo->burialPlaceId() !== null
            ? $entityOne->burialPlaceId()->isEqual($entityTwo->burialPlaceId())
            : $entityOne->burialPlaceId() === null && $entityTwo->burialPlaceId() === null;
        $isSameBurialPlaceOwnerId = $entityOne->burialPlaceOwnerId() !== null && $entityTwo->burialPlaceOwnerId() !== null
            ? $entityOne->burialPlaceOwnerId()->isEqual($entityTwo->burialPlaceOwnerId())
            : $entityOne->burialPlaceOwnerId() === null && $entityTwo->burialPlaceOwnerId() === null;
        $isSameFuneralCompanyId = $entityOne->funeralCompanyId() !== null && $entityTwo->funeralCompanyId() !== null
            ? $entityOne->funeralCompanyId()->isEqual($entityTwo->funeralCompanyId())
            : $entityOne->funeralCompanyId() === null && $entityTwo->funeralCompanyId() === null;
        $isSameBurialContainer = $entityOne->burialContainer() !== null && $entityTwo->burialContainer() !== null
            ? $entityOne->burialContainer()->isEqual($entityTwo->burialContainer())
            : $entityOne->burialContainer() === null && $entityTwo->burialContainer() === null;
        $isSameBuriedAt = $entityOne->buriedAt() !== null && $entityTwo->buriedAt() !== null
            ? $this->isEqualDateTimeValues($entityOne->buriedAt(), $entityTwo->buriedAt())
            : $entityOne->buriedAt() === null && $entityTwo->buriedAt() === null;

        return
            $isSameClass && $isSameId && $isSameCode && $isSameDeceasedId && $isSameBurialType && $isSameCustomerId &&
            $isSameBurialPlaceId && $isSameBurialPlaceOwnerId && $isSameFuneralCompanyId && $isSameBurialContainer &&
            $isSameBuriedAt;
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newBuriedAt           = null;
        $newBurialPlaceOwnerId = new NaturalPersonId('NP030');

        /** @var Burial $entityA */
        $entityA->setBuriedAt($newBuriedAt);
        $entityA->setBurialPlaceOwnerId($newBurialPlaceOwnerId);
    }
}
