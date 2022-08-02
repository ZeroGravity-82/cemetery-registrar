<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Query\ListCoffinShapes;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
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
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var ListCoffinShapesRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListCoffinShapesResponse($this->coffinShapeFetcher->findAll());
    }

    protected function supportedRequestClassName(): string
    {
        return ListCoffinShapesRequest::class;
    }
}
