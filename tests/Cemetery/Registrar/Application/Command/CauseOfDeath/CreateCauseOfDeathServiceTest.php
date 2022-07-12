<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\Command\CauseOfDeath;

use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathResponse;
use Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath\CreateCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    private MockObject|CauseOfDeathFactory $mockCauseOfDeathFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockCauseOfDeathFactory = $this->buildMockCauseOfDeathFactory();
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
        $this->mockCauseOfDeathFactory->expects($this->once())->method('create');
        $this->mockCauseOfDeathRepo->expects($this->once())->method('save')->with($this->mockCauseOfDeath);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathCreated &&
                    $arg->causeOfDeathId()->value() === $this->id;
            }),
        );

        $response = $this->service->execute(new CreateCauseOfDeathRequest('Панкреатит'));
        $this->assertInstanceOf(CreateCauseOfDeathResponse::class, $response);
        $this->assertSame($this->id, $response->id);
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->markTestIncomplete();
    }

    private function buildMockCauseOfDeathFactory(): MockObject|CauseOfDeathFactory
    {
        $mockCauseOfDeathFactory = $this->createMock(CauseOfDeathFactory::class);
        $mockCauseOfDeathFactory->method('create')->willReturn($this->mockCauseOfDeath);

        return $mockCauseOfDeathFactory;
    }
}
