<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\FuneralCompany\Query\CountFuneralCompanyTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
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
     * @param CountFuneralCompanyTotalRequest $request
     *
     * @return Notification
     */
    public function validate(ApplicationRequest $request): Notification
    {
        // TODO: Implement validate() method.
    }

    /**
     * @param CountFuneralCompanyTotalRequest $request
     *
     * @return ApplicationSuccessResponse
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
