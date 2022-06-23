<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\GraveSite\CountGraveSiteTotal;

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
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountGraveSiteTotalRequest::class;
    }

    /**
     * @param CountGraveSiteTotalRequest $request
     *
     * @return CountGraveSiteTotalResponse
     */
    public function execute($request): CountGraveSiteTotalResponse
    {
        return new CountGraveSiteTotalResponse($this->graveSiteFetcher->countTotal());
    }
}
