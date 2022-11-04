<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCemeteryBlocksService extends ApplicationService
{
    public function __construct(
        private readonly CemeteryBlockFetcher $cemeteryBlockFetcher,
        ListCemeteryBlocksRequestValidator    $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListCemeteryBlocksResponse(
            $this->cemeteryBlockFetcher->paginate(1),
            $this->cemeteryBlockFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListCemeteryBlocksRequest::class;
    }
}
