<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\ListOrganizations;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\Organization\OrganizationFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListOrganizationsService extends ApplicationService
{
    public function __construct(
        private readonly OrganizationFetcher $organizationFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListOrganizationsRequest::class;
    }

    /**
     * @param ListOrganizationsRequest $request
     *
     * @return ListOrganizationsResponse
     */
    public function execute($request): ListOrganizationsResponse
    {
        return new ListOrganizationsResponse($this->organizationFetcher->findAll(1));
    }
}
