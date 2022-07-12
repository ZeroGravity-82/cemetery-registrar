<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    public function setUp(): void
    {
        parent::setUp();

        $this->service = new EditCauseOfDeathService(
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
                    $arg instanceof CauseOfDeathEdited &&
                    $arg->causeOfDeathId()->value() === $this->id;
            }),
        );

        $response = $this->service->execute(new EditCauseOfDeathRequest($this->id, 'Аста кардиальная'));
        $this->assertInstanceOf(EditCauseOfDeathResponse::class, $response);
        $this->assertSame($this->id, $response->id);
    }

    public function testItFailsWhenNameAlreadyExists(): void
    {
        $this->markTestIncomplete();
    }

    public function testItFailsWhenAnCauseOfDeathIsNotFound(): void
    {
        $this->expectExceptionForNotFoundCauseOfDeathById();
        $this->service->execute(new EditCauseOfDeathRequest($this->unknownId, 'Новое наименование'));
    }
}
