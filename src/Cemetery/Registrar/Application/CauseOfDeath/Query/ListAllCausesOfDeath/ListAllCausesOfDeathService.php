<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListAllCausesOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllCausesOfDeathService extends AbstractApplicationService
{
    public function __construct(
        ListAllCausesOfDeathRequestValidator $requestValidator,
        private CauseOfDeathFetcherInterface $causeOfDeathFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
