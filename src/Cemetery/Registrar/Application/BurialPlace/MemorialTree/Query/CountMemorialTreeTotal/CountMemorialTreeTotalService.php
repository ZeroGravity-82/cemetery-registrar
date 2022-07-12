<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\CountMemorialTreeTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\MemorialTree\MemorialTreeFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountMemorialTreeTotalService extends ApplicationService
{
    public function __construct(
        private readonly MemorialTreeFetcher $memorialTreeFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountMemorialTreeTotalRequest::class;
    }

    /**
     * @param CountMemorialTreeTotalRequest $request
     *
     * @return CountMemorialTreeTotalResponse
     */
    public function execute($request): CountMemorialTreeTotalResponse
    {
        return new CountMemorialTreeTotalResponse($this->memorialTreeFetcher->countTotal());
    }
}
