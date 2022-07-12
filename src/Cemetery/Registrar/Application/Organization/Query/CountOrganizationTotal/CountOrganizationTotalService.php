<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\CountOrganizationTotal;

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
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountOrganizationTotalRequest::class;
    }

    /**
     * @param CountOrganizationTotalRequest $request
     *
     * @return CountOrganizationTotalResponse
     */
    public function execute($request): CountOrganizationTotalResponse
    {
        return new CountOrganizationTotalResponse($this->organizationFetcher->countTotal());
    }
}
