<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\CountGraveSiteTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\View\BurialPlace\GraveSite\GraveSiteFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountGraveSiteTotalService extends ApplicationService
{
    public function __construct(
        private readonly GraveSiteFetcher $graveSiteFetcher,
    ) {}

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var CountGraveSiteTotalRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new CountGraveSiteTotalResponse($this->graveSiteFetcher->countTotal());
    }

    protected function supportedRequestClassName(): string
    {
        return CountGraveSiteTotalRequest::class;
    }
}
