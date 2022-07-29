<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListCoffinShapes;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
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
     * @param ListCoffinShapesRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        return new ListCoffinShapesResponse($this->coffinShapeFetcher->findAll());
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return ListCoffinShapesRequest::class;
    }
}
