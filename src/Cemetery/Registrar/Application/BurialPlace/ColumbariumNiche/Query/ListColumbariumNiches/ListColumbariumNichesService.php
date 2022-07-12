<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumNichesService extends ApplicationService
{
    public function __construct(
        private readonly ColumbariumNicheFetcher $columbariumNicheFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListColumbariumNichesRequest::class;
    }

    /**
     * @param ListColumbariumNichesRequest $request
     *
     * @return ListColumbariumNichesResponse
     */
    public function execute($request): ListColumbariumNichesResponse
    {
        return new ListColumbariumNichesResponse($this->columbariumNicheFetcher->findAll(1));
    }
}
