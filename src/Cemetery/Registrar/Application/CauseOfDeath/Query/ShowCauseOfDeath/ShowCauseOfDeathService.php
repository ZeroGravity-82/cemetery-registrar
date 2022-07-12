<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathService extends ApplicationService
{
    public function __construct(
        private readonly CauseOfDeathFetcher $causeOfDeathFetcher,
    ) {}
    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ShowCauseOfDeathRequest::class;
    }

    /**
     * @param ShowCauseOfDeathRequest $request
     *
     * @return ShowCauseOfDeathResponse
     *
     * @throws \RuntimeException when the cause of death is not found by ID
     */
    public function execute($request): ShowCauseOfDeathResponse
    {
        $this->assertSupportedRequestClass($request);

        $causeOfDeathView = $this->causeOfDeathFetcher->findViewById($request->id);

        return new ShowCauseOfDeathResponse($causeOfDeathView);
    }
}
