<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Organization\CountOrganizationTotal;

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
     * @return CountOrganizationTotalResponse
     */
    public function execute($request): CountOrganizationTotalResponse
    {
        return new CountOrganizationTotalResponse($this->organizationFetcher->getTotalCount());
    }
}
