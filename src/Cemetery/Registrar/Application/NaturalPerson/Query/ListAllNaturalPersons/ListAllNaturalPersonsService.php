<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAllNaturalPersons;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAllNaturalPersonsService extends AbstractApplicationService
{
    public function __construct(
        ListAllNaturalPersonsRequestValidator $requestValidator,
        private NaturalPersonFetcherInterface $naturalPersonFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var ListAllNaturalPersonsRequest $request */
        return new ListAllNaturalPersonsResponse(
            $this->naturalPersonFetcher->findAll($request->term),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListAllNaturalPersonsRequest::class;
    }
}
