<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\CountFuneralCompanyTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
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
     * @param ApplicationRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new CountFuneralCompanyTotalResponse($this->funeralCompanyFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountFuneralCompanyTotalRequest::class;
    }
}
