<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ListGraveSites;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListGraveSitesService extends ApplicationService
{
    public function __construct(
        private readonly GraveSiteFetcher $graveSiteFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListGraveSitesRequest::class;
    }

    /**
     * @param ListGraveSitesRequest $request
     *
     * @return ListGraveSitesResponse
     */
    public function execute($request): ListGraveSitesResponse
    {
        return new ListGraveSitesResponse($this->graveSiteFetcher->findAll(1));
    }
}
