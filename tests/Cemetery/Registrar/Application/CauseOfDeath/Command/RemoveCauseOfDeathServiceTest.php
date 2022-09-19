<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    private MockObject|RemoveCauseOfDeathRequestValidator $mockRemoveCauseOfDeathRequestValidator;
    public function setUp(): void
    {
        parent::setUp();

        $this->mockRemoveCauseOfDeathRequestValidator = $this->createMock(RemoveCauseOfDeathRequestValidator::class);
        $this->service                                = new RemoveCauseOfDeathService(
            $this->mockCauseOfDeathRepo,
            $this->mockEventDispatcher,
            $this->mockRemoveCauseOfDeathRequestValidator,
        );
    }

    public function testItRemovesCauseOfDeath(): void
    {
        $this->mockCauseOfDeathRepo->expects($this->once())->method('findById')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathId &&
                    $arg->value() === $this->id;
            }),
        );
        $this->mockCauseOfDeathRepo->expects($this->once())->method('remove')->with($this->mockCauseOfDeath);
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathRemoved &&
                    $arg->id()->value() === $this->id;
            }),
        );

        $response = $this->service->execute(new RemoveCauseOfDeathRequest($this->id));
        $this->assertInstanceOf(ApplicationSuccessResponse::class, $response);
        $this->assertNull($response->data);
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveCauseOfDeathRequest::class;
    }
}
