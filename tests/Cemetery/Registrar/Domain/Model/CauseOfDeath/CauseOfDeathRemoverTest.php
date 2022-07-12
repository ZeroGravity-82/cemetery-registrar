<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\EventDispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemoverTest extends TestCase
{
    private MockObject|FuneralCompanyRepository $mockNaturalPersonRepo;
    private MockObject|BurialRepository         $mockBurialRepo;
    private MockObject|JuristicPersonRepository $mockJuristicPersonRepo;
    private MockObject|EventDispatcher          $mockEventDispatcher;
    private JuristicPersonRemover               $juristicPersonRemover;
    private MockObject|JuristicPerson           $mockJuristicPerson;
    private JuristicPersonId                    $juristicPersonId;
    private MockObject|FuneralCompany           $mockFuneralCompany;
    private FuneralCompanyId                    $funeralCompanyId;

    public function setUp(): void
    {
        $this->mockNaturalPersonRepo = $this->createMock(FuneralCompanyRepository::class);
        $this->mockBurialRepo         = $this->createMock(BurialRepository::class);
        $this->mockJuristicPersonRepo = $this->createMock(JuristicPersonRepository::class);
        $this->mockEventDispatcher    = $this->createMock(EventDispatcher::class);
        $this->juristicPersonRemover  = new JuristicPersonRemover(
            $this->mockNaturalPersonRepo,
            $this->mockBurialRepo,
            $this->mockJuristicPersonRepo,
            $this->mockEventDispatcher,
        );
        $this->mockJuristicPerson = $this->buildMockJuristicPerson();
        $this->mockFuneralCompany = $this->buildMockFuneralCompany();
    }

    public function testItRemovesCauseOfDeathWithoutRelatedEntities(): void
    {
        $this->mockNaturalPersonRepo->method('countByCauseOfDeathId')->willReturn(0);
        $this->mockJuristicPersonRepo->expects($this->once())->method('remove')->with($this->mockJuristicPerson);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof JuristicPersonRemoved &&
                    $arg->juristicPersonId()->isEqual($this->juristicPersonId);
            }),
        );
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    public function testItFailsToRemoveCauseOfDeathAssociatedWithDeceasedDetails(): void
    {
        $this->mockNaturalPersonRepo->method('findByOrganizationId')->willReturn($this->mockFuneralCompany);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(0);
        $this->mockJuristicPersonRepo->expects($this->never())->method('remove')->with($this->mockJuristicPerson);
        $this->mockEventDispatcher->expects($this->never())->method('dispatch');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Юридическое лицо не может быть удалено, т.к. оно связано с похоронной фирмой с ID "%s".',
            $this->funeralCompanyId->value(),
        ));
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    private function buildMockJuristicPerson(): MockObject|JuristicPerson
    {
        $this->juristicPersonId = new JuristicPersonId('777');
        $mockJuristicPerson     = $this->createMock(JuristicPerson::class);
        $mockJuristicPerson->method('id')->willReturn($this->juristicPersonId);

        return $mockJuristicPerson;
    }

    private function buildMockFuneralCompany(): MockObject|FuneralCompany
    {
        $this->funeralCompanyId = new FuneralCompanyId('888');
        $mockFuneralCompany = $this->createMock(FuneralCompany::class);
        $mockFuneralCompany->method('id')->willReturn($this->funeralCompanyId);

        return $mockFuneralCompany;
    }
}
