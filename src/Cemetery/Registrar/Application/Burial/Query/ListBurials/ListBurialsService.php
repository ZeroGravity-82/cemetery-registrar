<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListBurials;

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
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListBurialsRequest::class;
    }

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
