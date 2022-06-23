<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\ColumbariumNiche\CountColumbariumTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountColumbariumTotalService extends ApplicationService
{
    public function __construct(
        private readonly ColumbariumFetcher $columbariumFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountColumbariumTotalRequest::class;
    }

    /**
     * @param CountColumbariumTotalRequest $request
     *
     * @return CountColumbariumTotalResponse
     */
    public function execute($request): CountColumbariumTotalResponse
    {
        return new CountColumbariumTotalResponse($this->columbariumFetcher->countTotal());
    }
}
