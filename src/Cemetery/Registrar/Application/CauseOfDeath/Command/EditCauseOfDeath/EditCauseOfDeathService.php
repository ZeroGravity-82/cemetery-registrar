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
     * {@inheritdoc}
     */
    public function validate(ApplicationRequest $request): Notification
    {
        /** @var EditCauseOfDeathRequest $request */
        return $this->requestValidator->validate($request);
    }

    /**
     * {@inheritdoc}
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

        return new ApplicationSuccessResponse(
            ['id' => $causeOfDeath->id()->value()],
        );
    }
}
