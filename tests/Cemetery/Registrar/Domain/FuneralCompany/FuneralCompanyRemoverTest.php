<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\FuneralCompany;

use Cemetery\Registrar\Domain\Burial\BurialRepositoryInterface;
use Cemetery\Registrar\Domain\EventDispatcherInterface;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompany;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRemoved;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRemover;
use Cemetery\Registrar\Domain\FuneralCompany\FuneralCompanyRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class FuneralCompanyRemoverTest extends TestCase
{
    private MockObject|BurialRepositoryInterface         $mockBurialRepo;
    private MockObject|FuneralCompanyRepositoryInterface $mockFuneralCompanyRepo;
    private MockObject|EventDispatcherInterface          $mockEventDispatcher;
    private FuneralCompanyRemover                        $FuneralCompanyRemover;
    private MockObject|FuneralCompany                    $mockFuneralCompany;
    private FuneralCompanyId                             $funeralCompanyId;

    public function setUp(): void
    {
        $this->mockBurialRepo         = $this->createMock(BurialRepositoryInterface::class);
        $this->mockFuneralCompanyRepo = $this->createMock(FuneralCompanyRepositoryInterface::class);
        $this->mockEventDispatcher    = $this->createMock(EventDispatcherInterface::class);
        $this->FuneralCompanyRemover  = new FuneralCompanyRemover(
            $this->mockBurialRepo,
            $this->mockFuneralCompanyRepo,
            $this->mockEventDispatcher,
        );
        $this->mockFuneralCompany = $this->buildMockFuneralCompany();
    }

    public function testItRemovesAFuneralCompanyWithoutBurials(): void
    {
        $this->mockBurialRepo->method('countByFuneralCompanyId')->willReturn(0);
        $this->mockFuneralCompanyRepo->expects($this->once())->method('remove')->with($this->mockFuneralCompany);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return $arg instanceof FuneralCompanyRemoved &&
                    $arg->getFuneralCompanyId()->isEqual($this->funeralCompanyId);
            }),
        );
        $this->FuneralCompanyRemover->remove($this->mockFuneralCompany);
    }

    public function testItFailsToRemoveAFuneralCompanyWithBurials(): void
    {
        $this->mockBurialRepo->method('countByFuneralCompanyId')->willReturn(10);
        $this->mockFuneralCompanyRepo->expects($this->never())->method('remove')->with($this->mockFuneralCompany);
        $this->mockEventDispatcher->expects($this->never())->method('dispatch');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Похоронная компания не может быть удалена, т.к. она связана с 10 захоронениями.');
        $this->FuneralCompanyRemover->remove($this->mockFuneralCompany);
    }

    private function buildMockFuneralCompany(): MockObject|FuneralCompany
    {
        $this->funeralCompanyId = new FuneralCompanyId('777');
        $mockFuneralCompany     = $this->createMock(FuneralCompany::class);
        $mockFuneralCompany->method('getId')->willReturn($this->funeralCompanyId);

        return $mockFuneralCompany;
    }
}
