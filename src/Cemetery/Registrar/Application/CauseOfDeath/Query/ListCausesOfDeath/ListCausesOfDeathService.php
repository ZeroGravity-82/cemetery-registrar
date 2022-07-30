<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathService extends ApplicationService
{
    public function __construct(
        private readonly ListCausesOfDeathRequestValidator $requestValidator,
        private readonly CauseOfDeathFetcher               $causeOfDeathFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function validate(ApplicationRequest $request): Notification
    {
        /** @var ListCausesOfDeathRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var ListCausesOfDeathRequest $request */
        return new ApplicationSuccessResponse(
            ['list' => $this->causeOfDeathFetcher->findAll(1)],
        );
    }
}
