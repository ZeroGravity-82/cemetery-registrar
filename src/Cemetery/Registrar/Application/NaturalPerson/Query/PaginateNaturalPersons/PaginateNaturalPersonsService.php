<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\PaginateNaturalPersons;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateNaturalPersonsService extends ApplicationService
{
    public function __construct(
        private readonly NaturalPersonFetcher $naturalPersonFetcher,
        PaginateNaturalPersonsRequestValidator     $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new PaginateNaturalPersonsResponse(
            $this->naturalPersonFetcher->paginate(1),
            $this->naturalPersonFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return PaginateNaturalPersonsRequest::class;
    }
}
