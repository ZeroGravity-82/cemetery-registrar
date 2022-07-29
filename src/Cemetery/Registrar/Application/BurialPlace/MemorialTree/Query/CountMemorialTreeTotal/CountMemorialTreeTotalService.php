<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\MemorialTree\Query\CountMemorialTreeTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
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
     * @param CountMemorialTreeTotalRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new CountMemorialTreeTotalResponse($this->memorialTreeFetcher->countTotal());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CountMemorialTreeTotalRequest::class;
    }
}
