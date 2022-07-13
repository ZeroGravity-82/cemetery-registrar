<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath\RemoveCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemover;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    private MockObject|CauseOfDeathRemover $mockCauseOfDeathRemover;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockCauseOfDeathRemover = $this->createMock(CauseOfDeathRemover::class);
        $this->service                 = new RemoveCauseOfDeathService(
            $this->mockCauseOfDeathRemover,
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
        $this->mockCauseOfDeathRepo->expects($this->once())->method('findById')->with(
            $this->callback(function (object $arg) {
                return
                    $arg instanceof CauseOfDeathId &&
                    $arg->value() === $this->id;
            }),
        );
        $this->mockCauseOfDeathRemover->expects($this->once())->method('remove')->with($this->mockCauseOfDeath);
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
}
