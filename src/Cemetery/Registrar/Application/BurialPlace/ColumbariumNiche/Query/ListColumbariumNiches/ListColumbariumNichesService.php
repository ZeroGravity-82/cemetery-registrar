<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcherInterface;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumNichesService extends ApplicationService
{
    public function __construct(
        ListColumbariumNichesRequestValidator    $requestValidator,
        private ColumbariumNicheFetcherInterface $columbariumNicheFetcher,
        private ColumbariumFetcherInterface      $columbariumFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListColumbariumNichesResponse(
            $this->columbariumNicheFetcher->paginate(1),
            $this->columbariumNicheFetcher->countTotal(),
            $this->columbariumFetcher->paginate(1),
            $this->columbariumFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListColumbariumNichesRequest::class;
    }
}
