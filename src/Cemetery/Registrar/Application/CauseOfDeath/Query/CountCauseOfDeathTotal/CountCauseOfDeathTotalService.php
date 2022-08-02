<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\CountCauseOfDeathTotal;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CountCauseOfDeathTotalService extends ApplicationService
{
    public function __construct(
        private readonly CountCauseOfDeathTotalRequestValidator $requestValidator,
        private readonly CauseOfDeathFetcher                    $causeOfDeathFetcher,
    ) {}

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var CountCauseOfDeathTotalRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CountCauseOfDeathTotalRequest $request */
        return new ApplicationSuccessResponse(
            ['totalCount' => $this->causeOfDeathFetcher->countTotal()],
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CountCauseOfDeathTotalRequest::class;
    }
}
