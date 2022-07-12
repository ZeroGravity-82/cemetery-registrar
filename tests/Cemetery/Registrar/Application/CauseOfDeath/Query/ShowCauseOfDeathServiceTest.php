<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query;

use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Cemetery\Tests\Registrar\Application\ApplicationServiceTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathServiceTest extends ApplicationServiceTest
{
    private string                         $id;
    private string                         $unknownId;
    private CauseOfDeathView               $causeOfDeathView;
    private MockObject|CauseOfDeathFetcher $mockCauseOfDeathFetcher;

    public function setUp(): void
    {
        parent::setUp();

        $this->id               = 'CD001';
        $this->unknownId        = 'unknown_id';
        $this->causeOfDeathView = new CauseOfDeathView(
            $this->id,
            'Астма кардинальная',
            '2022-06-14 22:34:01',
            '2022-12-01 02:12:34',
        );
        $this->mockCauseOfDeathFetcher = $this->buildMockCauseOfDeathFetcher();
        $this->service = new ShowCauseOfDeathService(
            $this->mockCauseOfDeathFetcher,
        );
    }

    public function testItReturnsSupportedRequestClassName(): void
    {
        $this->assertSame(ShowCauseOfDeathRequest::class, $this->service->supportedRequestClassName());
    }
    
    public function testItShowsCauseOfDeath(): void
    {
        $this->mockCauseOfDeathFetcher->expects($this->once())->method('findViewById')->with($this->id);

        $response = $this->service->execute(new ShowCauseOfDeathRequest($this->id));
        $this->assertInstanceOf(ShowCauseOfDeathResponse::class, $response);
        $this->assertInstanceOf(CauseOfDeathView::class, $response->view);
        $this->assertSame($this->id, $response->view->id);
    }

    public function testItFailsWhenAnCauseOfDeathIsNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти с ID "%s" не найдена.', $this->unknownId));
        $this->service->execute(new ShowCauseOfDeathRequest($this->unknownId));
    }

    private function buildMockCauseOfDeathFetcher(): MockObject|CauseOfDeathFetcher
    {
        $mockCauseOfDeathFetcher = $this->createMock(CauseOfDeathFetcher::class);
        $mockCauseOfDeathFetcher->method('findViewById')->willReturnCallback(function (string $id) {
            return match ($id) {
                $this->id        => $this->causeOfDeathView,
                $this->unknownId => null,
            };
        });

        return $mockCauseOfDeathFetcher;
    }
}
