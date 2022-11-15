<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\ListMemorialTrees;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\AbstractApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcherInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListMemorialTreesService extends AbstractApplicationService
{
    public function __construct(
        ListMemorialTreesRequestValidator    $requestValidator,
        private MemorialTreeFetcherInterface $memorialTreeFetcher,
    ) {
        parent::__construct($requestValidator);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
