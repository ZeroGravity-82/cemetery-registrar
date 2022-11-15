<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Application\CauseOfDeath\Query;

use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathRequest;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathRequestValidator;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath\ShowCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcherInterface;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Cemetery\Tests\Registrar\Application\AbstractApplicationServiceTest;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathServiceTest extends AbstractApplicationServiceTest
{
    private string                                      $id;
    private string                                      $unknownId;
    private CauseOfDeathView                            $causeOfDeathView;
    private MockObject|CauseOfDeathFetcherInterface     $mockCauseOfDeathFetcher;
    private MockObject|ShowCauseOfDeathRequestValidator $mockShowCauseOfDeathRequestValidator;

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
        $this->mockCauseOfDeathFetcher              = $this->buildMockCauseOfDeathFetcher();
        $this->mockShowCauseOfDeathRequestValidator = $this->createMock(ShowCauseOfDeathRequestValidator::class);
        $this->service                              = new ShowCauseOfDeathService(
            $this->mockShowCauseOfDeathRequestValidator,
            $this->mockCauseOfDeathFetcher,
        );
    }

    public function testItShowsCauseOfDeath(): void
    {
        $this->mockCauseOfDeathFetcher->expects($this->once())->method('findViewById')->with($this->id);

        $response = $this->service->execute(new ShowCauseOfDeathRequest($this->id));
        $this->assertInstanceOf(ShowCauseOfDeathResponse::class, $response);
        $this->assertNotNull($response->data);
        $this->assertObjectHasAttribute('view', $response->data);
        $this->assertInstanceOf(CauseOfDeathView::class, $response->data->view);
        $this->assertSame($this->id, $response->data->view->id);
    }

    public function testItFailsWhenAnCauseOfDeathIsNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(\sprintf('Причина смерти с ID "%s" не найдена.', $this->unknownId));
        $this->service->execute(new ShowCauseOfDeathRequest($this->unknownId));
    }

    protected function supportedRequestClassName(): string
    {
        return ShowCauseOfDeathRequest::class;
    }

    private function buildMockCauseOfDeathFetcher(): MockObject|CauseOfDeathFetcherInterface
    {
        $mockCauseOfDeathFetcher = $this->createMock(CauseOfDeathFetcherInterface::class);
        $mockCauseOfDeathFetcher->method('findViewById')->willReturnCallback(function (string $id) {
            return match ($id) {
                $this->id        => $this->causeOfDeathView,
                $this->unknownId => null,
            };
        });

        return $mockCauseOfDeathFetcher;
    }
}
