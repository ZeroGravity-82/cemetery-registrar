<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\CauseOfDeath\CountCauseOfDeathTotal;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCauseOfDeathTotalService extends ApplicationService
{
    public function __construct(
        private readonly CauseOfDeathFetcher $causeOfDeathFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CountCauseOfDeathTotalRequest::class;
    }

    /**
     * @param CountCauseOfDeathTotalRequest $request
     *
     * @return CountCauseOfDeathTotalResponse
     */
    public function execute($request): CountCauseOfDeathTotalResponse
    {
        return new CountCauseOfDeathTotalResponse($this->causeOfDeathFetcher->countTotal());
    }
}
