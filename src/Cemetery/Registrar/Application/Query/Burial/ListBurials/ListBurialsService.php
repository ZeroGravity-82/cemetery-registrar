<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Burial\ListBurials;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\Burial\BurialFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListBurialsService extends ApplicationService
{
    public function __construct(
        private readonly BurialFetcher $burialFetcher,
    ) {}

    /**
     * @param ListBurialsRequest $request
     *
     * @return ListBurialsResponse
     */
    public function execute($request): ListBurialsResponse
    {
        return new ListBurialsResponse($this->burialFetcher->findAll(1));
    }
}