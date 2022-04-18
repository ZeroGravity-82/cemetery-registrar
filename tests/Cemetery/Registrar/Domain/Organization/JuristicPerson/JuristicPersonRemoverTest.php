<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\EventDispatcher;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRemoved;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRemover;
use Cemetery\Registrar\Domain\Organization\JuristicPerson\JuristicPersonRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemoverTest extends TestCase
{
    private MockObject|BurialRepositoryInterface         $mockBurialRepo;
    private MockObject|JuristicPersonRepositoryInterface $mockJuristicPersonRepo;
    private MockObject|EventDispatcher                   $mockEventDispatcher;
    private JuristicPersonRemover                        $juristicPersonRemover;
    private MockObject|JuristicPerson                    $mockJuristicPerson;
    private JuristicPersonId                             $juristicPersonId;

    public function setUp(): void
    {
        $this->mockBurialRepo         = $this->createMock(BurialRepositoryInterface::class);
        $this->mockJuristicPersonRepo = $this->createMock(JuristicPersonRepositoryInterface::class);
        $this->mockEventDispatcher    = $this->createMock(EventDispatcher::class);
        $this->juristicPersonRemover  = new JuristicPersonRemover(
            $this->mockBurialRepo,
            $this->mockJuristicPersonRepo,
            $this->mockEventDispatcher,
        );
        $this->mockJuristicPerson = $this->buildMockJuristicPerson();
    }

    public function testItRemovesAJuristicPersonWithoutRelatedEntities(): void
    {
        $this->mockBurialRepo->method('countByFuneralCompanyId')->willReturn(0);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(0);
        $this->mockJuristicPersonRepo->expects($this->once())->method('remove')->with($this->mockJuristicPerson);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return $arg instanceof JuristicPersonRemoved &&
                    $arg->juristicPersonId()->isEqual($this->juristicPersonId);
            }),
        );
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    public function testItFailsToRemoveAJuristicPersonWhichIsAFuneralCompanyForBurials(): void
    {
        $this->mockBurialRepo->method('countByFuneralCompanyId')->willReturn(10);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(0);
        $this->mockJuristicPersonRepo->expects($this->never())->method('remove')->with($this->mockJuristicPerson);
        $this->mockEventDispatcher->expects($this->never())->method('dispatch');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Юридическое лицо не может быть удалено, т.к. оно указано как похоронная фирма для 10 захоронений.');
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    public function testItFailsToRemoveAJuristicPersonWhichIsACustomerForBurials(): void
    {
        $this->mockBurialRepo->method('countByFuneralCompanyId')->willReturn(0);
        $this->mockBurialRepo->method('countByCustomerId')->willReturn(5);
        $this->mockJuristicPersonRepo->expects($this->never())->method('remove')->with($this->mockJuristicPerson);
        $this->mockEventDispatcher->expects($this->never())->method('dispatch');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Юридическое лицо не может быть удалено, т.к. оно указано как заказчик для 5 захоронений.');
        $this->juristicPersonRemover->remove($this->mockJuristicPerson);
    }

    private function buildMockJuristicPerson(): MockObject|JuristicPerson
    {
        $this->juristicPersonId = new JuristicPersonId('777');
        $mockJuristicPerson     = $this->createMock(JuristicPerson::class);
        $mockJuristicPerson->method('id')->willReturn($this->juristicPersonId);

        return $mockJuristicPerson;
    }
}
