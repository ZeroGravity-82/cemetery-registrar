<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\Command\CauseOfDeath;

use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathResponse;
use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Tests\Registrar\Application\ApplicationServiceTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathServiceTest extends ApplicationServiceTest
{
    private string                            $name;
    private string                            $id;
    private MockObject|CauseOfDeath           $mockCauseOfDeath;
    private MockObject|CauseOfDeathFactory    $mockCauseOfDeathFactory;
    private MockObject|CauseOfDeathRepository $mockCauseOfDeathRepo;
    private MockObject|EventDispatcher        $mockEventDispatcher;

    public function setUp(): void
    {
        $this->name = 'Онкология';
        $this->id   = 'CD001';

        $this->mockCauseOfDeath        = $this->buildMockCauseOfDeath();
        $this->mockCauseOfDeathFactory = $this->buildMockCauseOfDeathFactory();
        $this->mockCauseOfDeathRepo    = $this->createMock(CauseOfDeathRepository::class);
        $this->mockEventDispatcher     = $this->createMock(EventDispatcher::class);
        $this->service                 = new CreateCauseOfDeathService(
            $this->mockCauseOfDeathFactory,
            $this->mockCauseOfDeathRepo,
            $this->mockEventDispatcher,
        );
    }

    public function testItReturnsSupportedRequestClassName(): void
    {
        $this->assertSame(CreateCauseOfDeathRequest::class, $this->service->supportedRequestClassName());
    }

    public function testItCreatesCauseOfDeath(): void
    {
        $this->mockCauseOfDeathFactory->expects($this->once())->method('create')->with($this->name);
        $this->mockCauseOfDeathRepo->expects($this->once())->method('save')->with($this->mockCauseOfDeath);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathCreated             &&
                    $arg->causeOfDeathId()->value()   === $this->id &&
                    $arg->causeOfDeathName()->value() === $this->name;
            }),
        );

        $response = $this->service->execute(new CreateCauseOfDeathRequest($this->name));
        $this->assertInstanceOf(CreateCauseOfDeathResponse::class, $response);
        $this->assertSame($this->id, $response->causeOfDeathId);
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->markTestIncomplete();
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

    private function buildMockCauseOfDeathFactory(): MockObject|CauseOfDeathFactory
    {
        $mockCauseOfDeathFactory = $this->createMock(CauseOfDeathFactory::class);
        $mockCauseOfDeathFactory->method('create')->with($this->name)->willReturn($this->mockCauseOfDeath);

        return $mockCauseOfDeathFactory;
    }
}
