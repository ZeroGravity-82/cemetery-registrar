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
        PaginateNaturalPersonsRequestValidator $requestValidator,
        private NaturalPersonFetcher           $naturalPersonFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var PaginateNaturalPersonsRequest $request */
        return new PaginateNaturalPersonsResponse(
            $this->naturalPersonFetcher->paginate($request->page, $request->term),
            $this->naturalPersonFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return PaginateNaturalPersonsRequest::class;
    }
}
