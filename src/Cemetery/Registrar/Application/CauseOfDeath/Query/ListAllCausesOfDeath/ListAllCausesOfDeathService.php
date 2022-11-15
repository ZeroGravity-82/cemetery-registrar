<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListAllCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllCausesOfDeathService extends ApplicationService
{
    public function __construct(
        ListAllCausesOfDeathRequestValidator $requestValidator,
        private CauseOfDeathFetcher          $causeOfDeathFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var ListAllCausesOfDeathRequest $request */
        return new ListAllCausesOfDeathResponse(
            $this->causeOfDeathFetcher->findAll($request->term),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListAllCausesOfDeathRequest::class;
    }
}
