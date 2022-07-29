<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\NotFoundException;
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
     * @param ShowCauseOfDeathRequest $request
     *
     * @return ApplicationResponseSuccess
     *
     * @throws NotFoundException when no cause of death was found by the ID
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        $causeOfDeathView = $this->getCauseOfDeathView($request->id);

        return new ApplicationResponseSuccess(
            (object) ['view' => $causeOfDeathView],
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return ShowCauseOfDeathRequest::class;
    }

    /**
     * @param string $id
     *
     * @return CauseOfDeathView
     *
     * @throws NotFoundException when no cause of death was found by the ID
     */
    private function getCauseOfDeathView(string $id): CauseOfDeathView
    {
        $view = $this->causeOfDeathFetcher->findViewById($id);
        if ($view === null) {
            throw new NotFoundException(\sprintf('Причина смерти с ID "%s" не найдена.', $id));
        }

        return $view;
    }
}
