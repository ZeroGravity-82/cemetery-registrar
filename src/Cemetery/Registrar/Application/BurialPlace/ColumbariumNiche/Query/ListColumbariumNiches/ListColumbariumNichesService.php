<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\ColumbariumNiche\Query\ListColumbariumNiches;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumFetcher;
use Cemetery\Registrar\Domain\View\BurialPlace\ColumbariumNiche\ColumbariumNicheFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ListColumbariumNichesService extends ApplicationService
{
    public function __construct(
        private readonly ListColumbariumNichesRequestValidator $requestValidator,
        private readonly ColumbariumNicheFetcher               $columbariumNicheFetcher,
        private readonly ColumbariumFetcher                    $columbariumFetcher
    ) {}

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var ListColumbariumNichesRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ListColumbariumNichesResponse(
            $this->columbariumNicheFetcher->findAll(1),
            $this->columbariumNicheFetcher->countTotal(),
            $this->columbariumFetcher->findAll(1),
            $this->columbariumFetcher->countTotal(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ListColumbariumNichesRequest::class;
    }
}
