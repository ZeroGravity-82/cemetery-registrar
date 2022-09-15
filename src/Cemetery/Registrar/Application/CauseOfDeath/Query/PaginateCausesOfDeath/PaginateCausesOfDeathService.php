<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\PaginateCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateCausesOfDeathService extends ApplicationService
{
    public function __construct(
        private readonly CauseOfDeathFetcher  $causeOfDeathFetcher,
        PaginateCausesOfDeathRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var PaginateCausesOfDeathRequest $request */
        return new PaginateCausesOfDeathResponse(
            $this->causeOfDeathFetcher->paginate($request->page, $request->term),
            $this->causeOfDeathFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return PaginateCausesOfDeathRequest::class;
    }
}
