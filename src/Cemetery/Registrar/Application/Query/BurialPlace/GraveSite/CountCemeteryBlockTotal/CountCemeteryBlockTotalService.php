<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountCemeteryBlockTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\CemeteryBlockFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCemeteryBlockTotalService extends ApplicationService
{
    public function __construct(
        private readonly CemeteryBlockFetcher $cemeteryBlockFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountCemeteryBlockTotalRequest::class;
    }

    /**
     * @param CountCemeteryBlockTotalRequest $request
     *
     * @return CountCemeteryBlockTotalResponse
     */
    public function execute($request): CountCemeteryBlockTotalResponse
    {
        return new CountCemeteryBlockTotalResponse($this->cemeteryBlockFetcher->countTotal());
    }
}
