<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Burial\BurialRepository;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyRepository;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemover;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemoverTest extends TestCase
{
    private MockObject|FuneralCompanyRepository $mockFuneralCompanyRepo;
    private MockObject|BurialRepository         $mockBurialRepo;
    private MockObject|JuristicPersonRepository $mockJuristicPersonRepo;
    private JuristicPersonRemover               $juristicPersonRemover;
    private MockObject|JuristicPerson           $mockJuristicPerson;
    private MockObject|FuneralCompany           $mockFuneralCompany;

    public function setUp(): void
    {
        $this->mockFuneralCompanyRepo = $this->createMock(FuneralCompanyRepository::class);
        $this->mockBurialRepo         = $this->createMock(BurialRepository::class);
        $this->mockJuristicPersonRepo = $this->createMock(JuristicPersonRepository::class);
        $this->juristicPersonRemover  = new JuristicPersonRemover(
            $this->mockFuneralCompanyRepo,
            $this->mockBurialRepo,
            $this->mockJuristicPersonRepo,
        );
        $this->mockJuristicPerson = $this->createMock(JuristicPerson::class);
        $this->mockFuneralCompany = $this->createMock(FuneralCompany::class);
    }

    public function testItRemovesJuristicPersonWithoutRelatedEntities(): void
    {
        $this->mockFuneralCompanyRepo->method('findByOrganizationId')->willReturn(null);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(0);
        $this->mockJuristicPersonRepo->expects($this->once())->method('remove')->with($this->mockJuristicPerson);
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    public function testItFailsToRemoveJuristicPersonAssociatedWithFuneralCompany(): void
    {
        $this->mockFuneralCompanyRepo->method('findByOrganizationId')->willReturn($this->mockFuneralCompany);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(0);
        $this->mockJuristicPersonRepo->expects($this->never())->method('remove')->with($this->mockJuristicPerson);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Юридическое лицо не может быть удалено, т.к. оно связано с похоронной фирмой.');
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    public function testItFailsToRemoveJuristicPersonWhichIsCustomerForBurials(): void
    {
        $this->mockFuneralCompanyRepo->method('findByOrganizationId')->willReturn(null);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(5);
        $this->mockJuristicPersonRepo->expects($this->never())->method('remove')->with($this->mockJuristicPerson);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Юридическое лицо не может быть удалено, т.к. оно указано как заказчик для 5 захоронений.'
        );
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }
}
