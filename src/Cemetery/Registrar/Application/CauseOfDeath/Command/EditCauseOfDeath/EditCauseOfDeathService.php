<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Application\Notification;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathService extends CauseOfDeathService
{
    public function __construct(
        private readonly EditCauseOfDeathRequestValidator $requestValidator,
        CauseOfDeathRepository                            $causeOfDeathRepo,
        EventDispatcher                                   $eventDispatcher,
    ) {
        parent::__construct($causeOfDeathRepo, $eventDispatcher);
    }

    /**
     * @throws \InvalidArgumentException when the request is not an instance of the supported class
     */
    public function validate(ApplicationRequest $request): Notification
    {
        $this->assertSupportedRequestClass($request);

        /** @var EditCauseOfDeathRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * @throws NotFoundException when the cause of death is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var EditCauseOfDeathRequest $request */
        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $causeOfDeath->setName(new CauseOfDeathName($request->name));
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
}
