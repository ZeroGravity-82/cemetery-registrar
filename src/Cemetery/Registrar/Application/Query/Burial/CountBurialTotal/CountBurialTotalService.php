<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Burial\CountBurialTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountBurialTotalService extends ApplicationService
{
    public function __construct(
        private readonly BurialFetcher $burialFetcher,
    ) {}

    /**
     * @param CountBurialTotalRequest $request
     *
     * @return CountBurialTotalResponse
     */
    public function execute($request): CountBurialTotalResponse
    {
        return new CountBurialTotalResponse($this->burialFetcher->countTotal());
    }
}
