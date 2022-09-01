<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathService extends CauseOfDeathService
{
    public function __construct(
        CauseOfDeathRepository             $causeOfDeathRepo,
        EventDispatcher                    $eventDispatcher,
        RemoveCauseOfDeathRequestValidator $requestValidator,
    ) {
        parent::__construct($causeOfDeathRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the cause of death is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
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
