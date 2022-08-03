<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath\CreateCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    private MockObject|CreateCauseOfDeathRequestValidator $mockCreateCauseOfDeathRequestValidator;
    private MockObject|CauseOfDeathFactory                $mockCauseOfDeathFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockCreateCauseOfDeathRequestValidator = $this->createMock(CreateCauseOfDeathRequestValidator::class);
        $this->mockCauseOfDeathFactory                = $this->buildMockCauseOfDeathFactory();
        $this->service                                = new CreateCauseOfDeathService(
            $this->mockCreateCauseOfDeathRequestValidator,
            $this->mockCauseOfDeathFactory,
            $this->mockCauseOfDeathRepo,
            $this->mockEventDispatcher,
        );
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
        $this->assertNotNull($response->data);
        $this->assertObjectHasAttribute('id', $response->data);
        $this->assertSame($this->id, $response->data->id);
    }

    public function testItFailsWhenNameAlreadyUsed(): void
    {
        $this->markTestIncomplete();
    }

    protected function supportedRequestClassName(): string
    {
        return CreateCauseOfDeathRequest::class;
    }

    private function buildMockCauseOfDeathFactory(): MockObject|CauseOfDeathFactory
    {
        $mockCauseOfDeathFactory = $this->createMock(CauseOfDeathFactory::class);
        $mockCauseOfDeathFactory->method('create')->willReturn($this->mockCauseOfDeath);

        return $mockCauseOfDeathFactory;
    }
}
