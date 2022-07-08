<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\Command\CauseOfDeath;

use Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath\EditCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath\EditCauseOfDeathResponse;
use Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath\EditCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Tests\Registrar\Application\ApplicationServiceTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathServiceTest extends ApplicationServiceTest
{
    private string                            $name;
    private string                            $id;
    private MockObject|CauseOfDeath           $mockCauseOfDeath;
    private MockObject|CauseOfDeathRepository $mockCauseOfDeathRepo;
    private MockObject|EventDispatcher        $mockEventDispatcher;

    public function setUp(): void
    {
        $this->name = 'Онкология';
        $this->id   = 'CD001';

        $this->mockCauseOfDeath     = $this->buildMockCauseOfDeath();
        $this->mockCauseOfDeathRepo = $this->buildMockCauseOfDeathRepo();
        $this->mockEventDispatcher  = $this->createMock(EventDispatcher::class);
        $this->service              = new EditCauseOfDeathService(
            $this->mockCauseOfDeathRepo,
            $this->mockEventDispatcher,
        );
    }

    public function testItReturnsSupportedRequestClassName(): void
    {
        $this->assertSame(EditCauseOfDeathRequest::class, $this->service->supportedRequestClassName());
    }

    public function testItEditsCauseOfDeath(): void
    {
        // Testing itself
        $this->mockCauseOfDeathRepo->expects($this->once())->method('findById')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathId &&
                    $arg->value() === $this->id;
            }),
        );
        $this->mockCauseOfDeathRepo->expects($this->once())->method('save')->with($this->mockCauseOfDeath);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathEdited              &&
                    $arg->causeOfDeathId()->value()   === $this->id &&
                    $arg->causeOfDeathName()->value() === $this->name;
            }),
        );

        $response = $this->service->execute(new EditCauseOfDeathRequest($this->id, $this->name));
        $this->assertInstanceOf(EditCauseOfDeathResponse::class, $response);
        $this->assertSame($this->id, $response->causeOfDeathId);
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWhenAnCauseOfDeathIsNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти с ID "%s" не найдена.', 'unknown_id'));
        $this->service->execute(new EditCauseOfDeathRequest('unknown_id', 'Новое наименование'));
    }

    private function buildMockCauseOfDeath(): MockObject|CauseOfDeath
    {
        $mockCauseOfDeathId = $this->createMock(CauseOfDeathId::class);
        $mockCauseOfDeathId->method('value')->willReturn($this->id);
        $mockCauseOfDeathName = $this->createMock(CauseOfDeathName::class);
        $mockCauseOfDeathName->method('value')->willReturn($this->name);

        $mockCauseOfDeath = $this->createMock(CauseOfDeath::class);
        $mockCauseOfDeath->method('id')->willReturn($mockCauseOfDeathId);
        $mockCauseOfDeath->method('name')->willReturn($mockCauseOfDeathName);

        return $mockCauseOfDeath;
    }

    private function buildMockCauseOfDeathRepo(): MockObject|CauseOfDeathRepository
    {
        $mockCauseOfDeathRepo = $this->createMock(CauseOfDeathRepository::class);
        $mockCauseOfDeathRepo->method('findById')->willReturn($this->mockCauseOfDeath);

        return $mockCauseOfDeathRepo;
    }
}
