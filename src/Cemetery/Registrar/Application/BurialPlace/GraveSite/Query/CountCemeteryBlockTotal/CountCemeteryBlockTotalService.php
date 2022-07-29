<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\CountCemeteryBlockTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
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
     * @param CountCemeteryBlockTotalRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new CountCemeteryBlockTotalResponse($this->cemeteryBlockFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountCemeteryBlockTotalRequest::class;
    }
}
