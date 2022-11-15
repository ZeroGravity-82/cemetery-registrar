<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\AbstractCauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepositoryInterface;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathService extends AbstractCauseOfDeathService
{
    public function __construct(
        EditCauseOfDeathRequestValidator $requestValidator,
        CauseOfDeathRepositoryInterface  $causeOfDeathRepo,
        EventDispatcher                  $eventDispatcher,
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
        /** @var EditCauseOfDeathRequest $request */
        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $causeOfDeath->setName($this->buildName($request));
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathEdited(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new EditCauseOfDeathResponse(
            $causeOfDeath->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return EditCauseOfDeathRequest::class;
    }

    /**
     * @throws Exception when the name has invalid value
     */
    private function buildName(AbstractApplicationRequest $request): CauseOfDeathName
    {
        /** @var EditCauseOfDeathRequest $request */
        return new CauseOfDeathName($request->name);
    }
}
