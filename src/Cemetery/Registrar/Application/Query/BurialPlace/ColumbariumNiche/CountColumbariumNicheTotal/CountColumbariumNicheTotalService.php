<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\BurialPlace\ColumbariumNiche\CountColumbariumNicheTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountColumbariumNicheTotalService extends ApplicationService
{
    public function __construct(
        private readonly ColumbariumNicheFetcher $columbariumNicheFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountColumbariumNicheTotalRequest::class;
    }

    /**
     * @param CountColumbariumNicheTotalRequest $request
     *
     * @return CountColumbariumNicheTotalResponse
     */
    public function execute($request): CountColumbariumNicheTotalResponse
    {
        return new CountColumbariumNicheTotalResponse($this->columbariumNicheFetcher->countTotal());
    }
}
