<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\CauseOfDeath\Command;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Tests\Registrar\Application\ApplicationServiceTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CauseOfDeathServiceTest extends ApplicationServiceTest
{
    protected string                                     $id;
    protected string                                     $unknownId;
    protected MockObject|CauseOfDeath                    $mockCauseOfDeath;
    protected MockObject|CauseOfDeathRepositoryInterface $mockCauseOfDeathRepo;
    protected MockObject|EventDispatcher                 $mockEventDispatcher;

    public function setUp(): void
    {
        $this->id                   = 'CD001';
        $this->unknownId            = 'unknown_id';
        $this->mockCauseOfDeath     = $this->buildMockCauseOfDeath();
        $this->mockCauseOfDeathRepo = $this->buildMockCauseOfDeathRepo();
        $this->mockEventDispatcher  = $this->createMock(EventDispatcher::class);
    }

    protected function expectExceptionForNotFoundCauseOfDeathById(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти с ID "%s" не найдена.', $this->unknownId));
    }

    private function buildMockCauseOfDeath(): MockObject|CauseOfDeath
    {
        $mockCauseOfDeathId = $this->createMock(CauseOfDeathId::class);
        $mockCauseOfDeathId->method('value')->willReturn($this->id);

        $mockCauseOfDeath = $this->createMock(CauseOfDeath::class);
        $mockCauseOfDeath->method('id')->willReturn($mockCauseOfDeathId);

        return $mockCauseOfDeath;
    }

    private function buildMockCauseOfDeathRepo(): MockObject|CauseOfDeathRepositoryInterface
    {
        $mockCauseOfDeathRepo = $this->createMock(CauseOfDeathRepositoryInterface::class);
        $mockCauseOfDeathRepo->method('findById')->willReturnCallback(function (CauseOfDeathId $id) {
            return match ($id->value()) {
                $this->id        => $this->mockCauseOfDeath,
                $this->unknownId => null,
            };
        });

        return $mockCauseOfDeathRepo;
    }
}
