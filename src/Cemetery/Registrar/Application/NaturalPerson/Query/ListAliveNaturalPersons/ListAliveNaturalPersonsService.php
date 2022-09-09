<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Query\ListAliveNaturalPersons;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\NaturalPerson\NaturalPersonFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListAliveNaturalPersonsService extends ApplicationService
{
    public function __construct(
        private readonly NaturalPersonFetcher   $naturalPersonFetcher,
        ListAliveNaturalPersonsRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListAliveNaturalPersonsResponse(
            $this->naturalPersonFetcher->findAlive(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListAliveNaturalPersonsRequest::class;
    }
}
