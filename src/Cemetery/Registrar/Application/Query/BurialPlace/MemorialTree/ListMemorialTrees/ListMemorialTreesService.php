<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\MemorialTree\ListMemorialTrees;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListMemorialTreesService extends ApplicationService
{
    public function __construct(
        private readonly MemorialTreeFetcher $memorialTreeFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListMemorialTreesRequest::class;
    }

    /**
     * @param ListMemorialTreesRequest $request
     *
     * @return ListMemorialTreesResponse
     */
    public function execute($request): ListMemorialTreesResponse
    {
        return new ListMemorialTreesResponse($this->memorialTreeFetcher->findAll(1));
    }
}
