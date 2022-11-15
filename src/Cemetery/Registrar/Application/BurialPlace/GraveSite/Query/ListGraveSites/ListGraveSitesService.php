<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListGraveSitesService extends ApplicationService
{
    public function __construct(
        private GraveSiteFetcher       $graveSiteFetcher,
        private CemeteryBlockFetcher   $cemeteryBlockFetcher,
        ListGraveSitesRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
