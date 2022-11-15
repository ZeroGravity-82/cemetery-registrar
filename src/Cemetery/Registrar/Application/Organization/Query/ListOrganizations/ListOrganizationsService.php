<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Organization\Query\ListOrganizations;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\Organization\OrganizationFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListOrganizationsService extends AbstractApplicationService
{
    public function __construct(
        ListOrganizationsRequestValidator    $requestValidator,
        private OrganizationFetcherInterface $organizationFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListOrganizationsResponse(
            $this->organizationFetcher->paginate(1),
            $this->organizationFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListOrganizationsRequest::class;
    }
}
