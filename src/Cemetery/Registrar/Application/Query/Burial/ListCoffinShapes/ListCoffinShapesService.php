<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Query\Burial\ListCoffinShapes;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\View\Burial\BurialContainer\CoffinShapeFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListCoffinShapesService extends ApplicationService
{
    public function __construct(
        private readonly CoffinShapeFetcher $coffinShapeFetcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return ListCoffinShapesRequest::class;
    }

    /**
     * @param ListCoffinShapesRequest $request
     *
     * @return ListCoffinShapesResponse
     */
    public function execute($request): ListCoffinShapesResponse
    {
        return new ListCoffinShapesResponse($this->coffinShapeFetcher->findAll());
    }
}
