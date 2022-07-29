<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\CountGraveSiteTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountGraveSiteTotalService extends ApplicationService
{
    public function __construct(
        private readonly GraveSiteFetcher $graveSiteFetcher,
    ) {}

    /**
     * @param CountGraveSiteTotalRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new CountGraveSiteTotalResponse($this->graveSiteFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountGraveSiteTotalRequest::class;
    }
}
