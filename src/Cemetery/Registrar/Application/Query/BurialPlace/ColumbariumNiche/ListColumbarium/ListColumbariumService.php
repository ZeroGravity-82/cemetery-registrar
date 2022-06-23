<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\ColumbariumNiche\ListColumbarium;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumService extends ApplicationService
{
    public function __construct(
        private readonly ColumbariumFetcher $columbariumFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListColumbariumRequest::class;
    }

    /**
     * @param ListColumbariumRequest $request
     *
     * @return ListColumbariumResponse
     */
    public function execute($request): ListColumbariumResponse
    {
        return new ListColumbariumResponse($this->columbariumFetcher->findAll());
    }
}
