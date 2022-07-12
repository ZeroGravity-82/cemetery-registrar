<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\Command\CauseOfDeath;

use Cemetery\Registrar\Application\Command\CauseOfDeath\RemoveCauseOfDeath\RemoveCauseOfDeathRequest;
use Cemetery\Registrar\Application\Command\CauseOfDeath\RemoveCauseOfDeath\RemoveCauseOfDeathResponse;
use Cemetery\Registrar\Application\Command\CauseOfDeath\RemoveCauseOfDeath\RemoveCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new RemoveCauseOfDeathService(
            $this->mockCauseOfDeathRepo,
            $this->mockEventDispatcher,
        );
    }

    public function testItReturnsSupportedRequestClassName(): void
    {
        $this->assertSame(RemoveCauseOfDeathRequest::class, $this->service->supportedRequestClassName());
    }

    public function testItRemovesCauseOfDeath(): void
    {
        // Testing itself
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
                    $arg->causeOfDeathId()->value() === $this->id;
            }),
        );

        $this->assertNull(
            $this->service->execute(new RemoveCauseOfDeathRequest($this->id))
        );
    }

    public function testItFailsWhenAnCauseOfDeathIsNotFound(): void
    {
        $this->expectExceptionForNotFoundCauseOfDeathById();
        $this->service->execute(new RemoveCauseOfDeathRequest($this->unknownId));
    }
}
