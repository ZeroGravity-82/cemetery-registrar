<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Entity;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCollection;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryValidator;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Orm\Repository\DoctrineOrmNaturalPersonRepository;
use DataFixtures\NaturalPerson\NaturalPersonProvider;

/**
 * @group database
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DoctrineOrmNaturalPersonRepositoryIntegrationTest extends DoctrineOrmRepositoryIntegrationTest
{
    protected string $entityClassName           = NaturalPerson::class;
    protected string $entityIdClassName         = NaturalPersonId::class;
    protected string $entityCollectionClassName = NaturalPersonCollection::class;

    private NaturalPerson $entityD;
    private NaturalPerson $entityE;
    private NaturalPerson $entityF;
    private NaturalPerson $entityG;
    private NaturalPerson $entityH;
    private NaturalPerson $entityI;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockRepositoryValidator = $this->createMock(NaturalPersonRepositoryValidator::class);
        $this->repo                    = new DoctrineOrmNaturalPersonRepository(
            $this->entityManager,
            $this->mockRepositoryValidator,
        );
        $this->entityA = NaturalPersonProvider::getNaturalPersonA();
        $this->entityB = NaturalPersonProvider::getNaturalPersonB();
        $this->entityC = NaturalPersonProvider::getNaturalPersonC();
        $this->entityD = NaturalPersonProvider::getNaturalPersonD();
        $this->entityE = NaturalPersonProvider::getNaturalPersonE();
        $this->entityF = NaturalPersonProvider::getNaturalPersonF();
        $this->entityG = NaturalPersonProvider::getNaturalPersonG();
        $this->entityH = NaturalPersonProvider::getNaturalPersonH();
        $this->entityI = NaturalPersonProvider::getNaturalPersonI();
    }

    public function testItReturnsSupportedAggregateRootClassName(): void
    {
        $this->assertSame(NaturalPerson::class, $this->repo->supportedAggregateRootClassName());
    }

    public function testItReturnsSupportedAggregateRootIdClassName(): void
    {
        $this->assertSame(NaturalPersonId::class, $this->repo->supportedAggregateRootIdClassName());
    }

    public function testItReturnsSupportedAggregateRootCollectionClassName(): void
    {
        $this->assertSame(NaturalPersonCollection::class, $this->repo->supportedAggregateRootCollectionClassName());
    }

    public function testItCountsNaturalPersonsByCauseOfDeathId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
            $this->entityI
        ]));
        $this->entityManager->clear();

        // Testing itself
        $knownCauserOfDeathId = new CauseOfDeathId('CD004');
        $naturalPersonCount   = $this->repo->countByCauseOfDeathId($knownCauserOfDeathId);
        $this->assertSame(2, $naturalPersonCount);

        $knownCauserOfDeathId = new CauseOfDeathId('CD008');
        $naturalPersonCount   = $this->repo->countByCauseOfDeathId($knownCauserOfDeathId);
        $this->assertSame(1, $naturalPersonCount);

        $unknownCauserOfDeathId = new CauseOfDeathId('unknown_id');
        $naturalPersonCount     = $this->repo->countByCauseOfDeathId($unknownCauserOfDeathId);
        $this->assertSame(0, $naturalPersonCount);
    }

    public function testItDoesNotCountRemovedNaturalPersonsByCauseOfDeathId(): void
    {
        // Prepare the repo for testing
        $this->repo->saveAll(new NaturalPersonCollection([
            $this->entityA,
            $this->entityB,
            $this->entityC,
            $this->entityD,
            $this->entityE,
            $this->entityF,
            $this->entityG,
            $this->entityH,
            $this->entityI
        ]));
        $this->entityManager->clear();

        // Testing itself
        /** @var NaturalPerson $persistedEntityB */
        $persistedEntityB = $this->repo->findById($this->entityB->id());
        $causeOfDeathIdB  = $persistedEntityB->deceasedDetails()->causeOfDeathId();
        $this->repo->remove($persistedEntityB);
        $this->entityManager->clear();

        $naturalPersonCount = $this->repo->countByCauseOfDeathId($causeOfDeathIdB);
        $this->assertSame(0, $naturalPersonCount);
    }

    protected function areEqualEntities(Entity $entityOne, Entity $entityTwo): bool
    {
        /** @var NaturalPerson $entityOne */
        /** @var NaturalPerson $entityTwo */
        return
            $this->areSameClasses($entityOne, $entityTwo) &&
            $this->areEqualValueObjects($entityOne->id(), $entityTwo->id()) &&
            $this->areEqualValueObjects($entityOne->fullName(), $entityTwo->fullName()) &&
            $this->areEqualValueObjects($entityOne->phone(), $entityTwo->phone()) &&
            $this->areEqualValueObjects($entityOne->phoneAdditional(), $entityTwo->phoneAdditional()) &&
            $this->areEqualValueObjects($entityOne->email(), $entityTwo->email()) &&
            $this->areEqualValueObjects($entityOne->address(), $entityTwo->address()) &&
            $this->areEqualDateTimeValues($entityOne->bornAt(), $entityTwo->bornAt()) &&
            $this->areEqualValueObjects($entityOne->placeOfBirth(), $entityTwo->placeOfBirth()) &&
            $this->areEqualValueObjects($entityOne->passport(), $entityTwo->passport()) &&
            $this->areEqualValueObjects($entityOne->deceasedDetails(), $entityTwo->deceasedDetails());
    }

    protected function updateEntityA(Entity $entityA): void
    {
        $newBornAt = new \DateTimeImmutable('2003-03-01');

        /** @var NaturalPerson $entityA */
        $entityA->setBornAt($newBornAt);
    }
}
