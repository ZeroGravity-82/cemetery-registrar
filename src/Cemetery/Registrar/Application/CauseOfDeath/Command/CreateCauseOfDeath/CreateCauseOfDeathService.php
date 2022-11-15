<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\AbstractCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathService extends AbstractCauseOfDeathService
{
    public function __construct(
        CreateCauseOfDeathRequestValidator $requestValidator,
        CauseOfDeathRepositoryInterface    $causeOfDeathRepo,
        EventDispatcher                    $eventDispatcher,
        private CauseOfDeathFactory        $causeOfDeathFactory,
    ) {
        parent::__construct($requestValidator, $causeOfDeathRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateCauseOfDeathRequest $request */
        $causeOfDeath = $this->causeOfDeathFactory->create(
            $request->name,
        );
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathCreated(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new CreateCauseOfDeathResponse(
            $causeOfDeath->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateCauseOfDeathRequest::class;
    }
}
