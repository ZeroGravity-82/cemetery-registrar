<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\AbstractCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathService extends AbstractCauseOfDeathService
{
    public function __construct(
        RemoveCauseOfDeathRequestValidator $requestValidator,
        CauseOfDeathRepositoryInterface    $causeOfDeathRepo,
        EventDispatcher                    $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $causeOfDeathRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the cause of death is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var RemoveCauseOfDeathRequest $request */
        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $this->causeOfDeathRepo->remove($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathRemoved($causeOfDeath->id()));

        return new ApplicationSuccessResponse();
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveCauseOfDeathRequest::class;
    }
}
