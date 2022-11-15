<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAliveNaturalPersons;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAliveNaturalPersonsService extends AbstractApplicationService
{
    public function __construct(
        ListAliveNaturalPersonsRequestValidator $requestValidator,
        private NaturalPersonFetcherInterface   $naturalPersonFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var ListAliveNaturalPersonsRequest $request */
        return new ListAliveNaturalPersonsResponse(
            $this->naturalPersonFetcher->findAlive($request->term),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListAliveNaturalPersonsRequest::class;
    }
}
