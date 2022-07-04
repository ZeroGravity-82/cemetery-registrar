<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\CauseOfDeath\CreateCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathService extends ApplicationService
{
    /**
     * @param CauseOfDeathFactory    $causeOfDeathFactory
     * @param CauseOfDeathRepository $causeOfDeathRepo
     * @param EventDispatcher        $eventDispatcher
     */
    public function __construct(
        private readonly CauseOfDeathFactory    $causeOfDeathFactory,
        private readonly CauseOfDeathRepository $causeOfDeathRepo,
        private readonly EventDispatcher        $eventDispatcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return CreateCauseOfDeathRequest::class;
    }

    /**
     * @param CreateCauseOfDeathRequest $request
     *
     * @return CreateCauseOfDeathResponse
     */
    public function execute($request): CreateCauseOfDeathResponse
    {
        $this->assertSupportedRequestClass($request);

        // TODO add uniqueness check
        $causeOfDeath = $this->causeOfDeathFactory->create(
            $request->name,
        );
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathCreated(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new CreateCauseOfDeathResponse($causeOfDeath->id()->value());
    }
}
