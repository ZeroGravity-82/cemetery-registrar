<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;

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
     * @return ApplicationResponseSuccess
     *
     * @throws \RuntimeException when the cause of death is not found by ID
     */
    public function execute($request): ApplicationResponseSuccess
    {
        $this->assertSupportedRequestClass($request);

        $causeOfDeathView = $this->getCauseOfDeathView($request->id);

        return new ApplicationResponseSuccess($causeOfDeathView);
    }

    /**
     * @param string $id
     *
     * @return CauseOfDeathView
     *
     * @throws \RuntimeException when no data was found by the ID
     */
    private function getCauseOfDeathView(string $id): CauseOfDeathView
    {
        $view = $this->causeOfDeathFetcher->findViewById($id);
        if ($view === null) {
            throw new \RuntimeException(\sprintf('Причина смерти с ID "%s" не найдена.', $id));
        }

        return $view;
    }
}
