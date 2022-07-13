<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemover;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemoverTest extends TestCase
{
    private MockObject|NaturalPersonRepository $mockNaturalPersonRepo;
    private MockObject|CauseOfDeathRepository  $mockCauseOfDeathRepo;
    private CauseOfDeathRemover                $causeOfDeathRemover;
    private MockObject|CauseOfDeath            $mockCauseOfDeath;

    public function setUp(): void
    {
        $this->mockNaturalPersonRepo = $this->createMock(NaturalPersonRepository::class);
        $this->mockCauseOfDeathRepo  = $this->createMock(CauseOfDeathRepository::class);
        $this->causeOfDeathRemover   = new CauseOfDeathRemover(
            $this->mockNaturalPersonRepo,
            $this->mockCauseOfDeathRepo,
        );
        $this->mockCauseOfDeath = $this->createMock(CauseOfDeath::class);
    }

    public function testItRemovesCauseOfDeathWithoutRelatedEntities(): void
    {
        $this->mockNaturalPersonRepo->method('countByCauseOfDeathId')->willReturn(0);
        $this->mockCauseOfDeathRepo->expects($this->once())->method('remove')->with($this->mockCauseOfDeath);
        $this->causeOfDeathRemover->remove($this->mockCauseOfDeath);
    }

    public function testItFailsToRemoveCauseOfDeathAssociatedWithDeceasedDetails(): void
    {
        $this->mockNaturalPersonRepo->method('countByCauseOfDeathId')->willReturn(5);
        $this->mockCauseOfDeathRepo->expects($this->never())->method('remove')->with($this->mockCauseOfDeath);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Причина смерти не может быть удалена, т.к. она указана для 5 умерших.');
        $this->causeOfDeathRemover->remove($this->mockCauseOfDeath);
    }
}
