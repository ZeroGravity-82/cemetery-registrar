<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath\EditCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathServiceTest extends CauseOfDeathServiceTest
{
    private MockObject|EditCauseOfDeathRequestValidator $mockEditCauseOfDeathRequestValidator;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockEditCauseOfDeathRequestValidator = $this->createMock(EditCauseOfDeathRequestValidator::class);
        $this->service                              = new EditCauseOfDeathService(
            $this->mockEditCauseOfDeathRequestValidator,
            $this->mockCauseOfDeathRepo,
            $this->mockEventDispatcher,
        );
    }

    public function testItEditsCauseOfDeath(): void
    {
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
        return EditCauseOfDeathRequest::class;
    }
}
