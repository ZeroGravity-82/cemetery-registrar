<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\FuneralCompany\CountFuneralCompanyTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\FuneralCompany\FuneralCompanyFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountFuneralCompanyTotalService extends ApplicationService
{
    public function __construct(
        private readonly FuneralCompanyFetcher $funeralCompanyFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountFuneralCompanyTotalRequest::class;
    }

    /**
     * @param CountFuneralCompanyTotalRequest $request
     *
     * @return CountFuneralCompanyTotalResponse
     */
    public function execute($request): CountFuneralCompanyTotalResponse
    {
        return new CountFuneralCompanyTotalResponse($this->funeralCompanyFetcher->countTotal());
    }
}
