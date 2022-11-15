<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListMemorialTreesService extends ApplicationService
{
    public function __construct(
        private MemorialTreeFetcher       $memorialTreeFetcher,
        ListMemorialTreesRequestValidator $requestValidator,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListMemorialTreesResponse(
            $this->memorialTreeFetcher->paginate(1),
            $this->memorialTreeFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListMemorialTreesRequest::class;
    }
}
