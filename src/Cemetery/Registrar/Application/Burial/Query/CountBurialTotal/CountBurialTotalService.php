<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\CountBurialTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
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
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new CountBurialTotalResponse($this->burialFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountBurialTotalRequest::class;
    }
}
