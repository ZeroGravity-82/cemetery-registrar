<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathService extends CauseOfDeathService
{
    /**
     * @param CauseOfDeathFactory    $causeOfDeathFactory
     * @param CauseOfDeathRepository $causeOfDeathRepo
     * @param EventDispatcher        $eventDispatcher
     */
    public function __construct(
        private readonly CauseOfDeathFactory $causeOfDeathFactory,
        CauseOfDeathRepository               $causeOfDeathRepo,
        EventDispatcher                      $eventDispatcher,
    ) {
        parent::__construct($causeOfDeathRepo, $eventDispatcher);
    }

    /**
     * @param CreateCauseOfDeathRequest $request
     *
     * @return ApplicationResponseSuccess
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        $causeOfDeath = $this->causeOfDeathFactory->create(
            $request->name,
        );
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathCreated(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new ApplicationResponseSuccess(
            (object) ['id' => $causeOfDeath->id()->value()],
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return CreateCauseOfDeathRequest::class;
    }
}
