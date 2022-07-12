<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ListCausesOfDeath;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCausesOfDeathService extends ApplicationService
{
    public function __construct(
        private readonly CauseOfDeathFetcher $causeOfDeathFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListCausesOfDeathRequest::class;
    }

    /**
     * @param ListCausesOfDeathRequest $request
     *
     * @return ListCausesOfDeathResponse
     */
    public function execute($request): ListCausesOfDeathResponse
    {
        return new ListCausesOfDeathResponse($this->causeOfDeathFetcher->findAll(1));
    }
}
