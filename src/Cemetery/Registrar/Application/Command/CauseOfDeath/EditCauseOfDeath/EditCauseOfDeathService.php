<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeath;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathService extends ApplicationService
{
    /**
     * @param CauseOfDeathRepository $causeOfDeathRepo
     * @param EventDispatcher        $eventDispatcher
     */
    public function __construct(
        private readonly CauseOfDeathRepository $causeOfDeathRepo,
        private readonly EventDispatcher        $eventDispatcher,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return EditCauseOfDeathRequest::class;
    }

    /**
     * @param EditCauseOfDeathRequest $request
     *
     * @return EditCauseOfDeathResponse
     */
    public function execute($request): EditCauseOfDeathResponse
    {
        $this->assertSupportedRequestClass($request);

        // TODO add uniqueness check
        /** @var CauseOfDeath $causeOfDeath */
        $causeOfDeath = $this->causeOfDeathRepo->findById(
            new CauseOfDeathId($request->id),
        );
        $causeOfDeath->setName(new CauseOfDeathName($request->name));
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathEdited(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new EditCauseOfDeathResponse($causeOfDeath->id()->value());
    }
}
