<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListFuneralCompaniesService extends AbstractApplicationService
{
    public function __construct(
        ListFuneralCompaniesRequestValidator  $requestValidator,
        private FuneralCompanyFetcherInterface $funeralCompanyFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListFuneralCompaniesResponse(
            $this->funeralCompanyFetcher->paginate(1),
            $this->funeralCompanyFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListFuneralCompaniesRequest::class;
    }
}
