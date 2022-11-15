<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\PaginateCausesOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateCausesOfDeathService extends AbstractApplicationService
{
    public function __construct(
        PaginateCausesOfDeathRequestValidator $requestValidator,
        private CauseOfDeathFetcherInterface  $causeOfDeathFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
