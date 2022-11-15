<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCemeteryBlocksService extends AbstractApplicationService
{
    public function __construct(
        ListCemeteryBlocksRequestValidator    $requestValidator,
        private CemeteryBlockFetcherInterface $cemeteryBlockFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
