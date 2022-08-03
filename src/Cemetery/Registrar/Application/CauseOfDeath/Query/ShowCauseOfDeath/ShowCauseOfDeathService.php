<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Query\ShowCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathFetcher;
use Cemetery\Registrar\Domain\View\CauseOfDeath\CauseOfDeathView;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowCauseOfDeathService extends ApplicationService
{
    public function __construct(
        private readonly ShowCauseOfDeathRequestValidator $requestValidator,
        private readonly CauseOfDeathFetcher              $causeOfDeathFetcher,
    ) {}

    /**
     * @param ShowCauseOfDeathRequest $request
     *
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        return $this->requestValidator->validate($request);
    }

    /**
     * @param ShowCauseOfDeathRequest $request
     *
     * @throws NotFoundHttpException when the cause of death is not found
     * @throws \Throwable            when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        return new ShowCauseOfDeathResponse(
            $this->getCauseOfDeathView($request->id),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ShowCauseOfDeathRequest::class;
    }

    /**
     * @throws NotFoundException when the cause of death is not found
     */
    private function getCauseOfDeathView(string $id): CauseOfDeathView
    {
        $view = $this->causeOfDeathFetcher->findViewById($id);
        if ($view === null) {
            throw new NotFoundException(\sprintf('Причина смерти с ID "%s" не найдена.', $id));
        }

        return $view;
    }
}
