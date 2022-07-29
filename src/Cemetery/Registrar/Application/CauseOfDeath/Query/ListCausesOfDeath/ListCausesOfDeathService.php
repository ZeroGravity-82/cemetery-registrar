<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathService extends ApplicationService
{
    public function __construct(
        private readonly CauseOfDeathFetcher $causeOfDeathFetcher,
    ) {}

    /**
     * @param ListCausesOfDeathRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new ApplicationResponseSuccess(
            (object) ['list' => $this->causeOfDeathFetcher->findAll(1)],
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return ListCausesOfDeathRequest::class;
    }
}
