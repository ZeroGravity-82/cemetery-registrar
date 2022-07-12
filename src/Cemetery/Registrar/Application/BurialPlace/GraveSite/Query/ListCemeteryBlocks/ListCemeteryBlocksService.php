<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListCemeteryBlocks;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCemeteryBlocksService extends ApplicationService
{
    public function __construct(
        private readonly CemeteryBlockFetcher $cemeteryBlockFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListCemeteryBlocksRequest::class;
    }

    /**
     * @param ListCemeteryBlocksRequest $request
     *
     * @return ListCemeteryBlocksResponse
     */
    public function execute($request): ListCemeteryBlocksResponse
    {
        return new ListCemeteryBlocksResponse($this->cemeteryBlockFetcher->findAll());
    }
}
