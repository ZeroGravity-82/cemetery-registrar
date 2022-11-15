<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListFuneralCompaniesService extends ApplicationService
{
    public function __construct(
        private FuneralCompanyFetcher        $funeralCompanyFetcher,
        ListFuneralCompaniesRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
