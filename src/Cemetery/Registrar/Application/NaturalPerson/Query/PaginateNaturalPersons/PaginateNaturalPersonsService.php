<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\PaginateNaturalPersons;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PaginateNaturalPersonsService extends AbstractApplicationService
{
    public function __construct(
        PaginateNaturalPersonsRequestValidator $requestValidator,
        private NaturalPersonFetcherInterface  $naturalPersonFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
