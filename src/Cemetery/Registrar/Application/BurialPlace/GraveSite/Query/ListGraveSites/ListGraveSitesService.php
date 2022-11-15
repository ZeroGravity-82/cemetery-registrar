<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListGraveSitesService extends AbstractApplicationService
{
    public function __construct(
        ListGraveSitesRequestValidator        $requestValidator,
        private GraveSiteFetcherInterface     $graveSiteFetcher,
        private CemeteryBlockFetcherInterface $cemeteryBlockFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListGraveSitesResponse(
            $this->graveSiteFetcher->paginate(1),
            $this->graveSiteFetcher->countTotal(),
            $this->cemeteryBlockFetcher->paginate(1),
            $this->cemeteryBlockFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListGraveSitesRequest::class;
    }
}
