<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\CountOrganizationTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\Organization\OrganizationFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountOrganizationTotalService extends ApplicationService
{
    public function __construct(
        private readonly OrganizationFetcher $organizationFetcher,
    ) {}

    /**
     * @param CountOrganizationTotalRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new CountOrganizationTotalResponse($this->organizationFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountOrganizationTotalRequest::class;
    }
}
