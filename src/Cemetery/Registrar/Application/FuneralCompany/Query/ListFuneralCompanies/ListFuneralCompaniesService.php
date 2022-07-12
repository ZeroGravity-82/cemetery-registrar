<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\ListFuneralCompanies;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListFuneralCompaniesService extends ApplicationService
{
    public function __construct(
        private readonly FuneralCompanyFetcher $funeralCompanyFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListFuneralCompaniesRequest::class;
    }

    /**
     * @param ListFuneralCompaniesRequest $request
     *
     * @return ListFuneralCompaniesResponse
     */
    public function execute($request): ListFuneralCompaniesResponse
    {
        return new ListFuneralCompaniesResponse($this->funeralCompanyFetcher->findAll(1));
    }
}
