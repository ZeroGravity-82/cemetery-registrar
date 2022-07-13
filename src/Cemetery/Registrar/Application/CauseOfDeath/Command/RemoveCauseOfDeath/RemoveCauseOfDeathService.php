<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemover;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathService extends CauseOfDeathService
{
    public function __construct(
        private readonly CauseOfDeathRemover $causeOfDeathRemover,
        CauseOfDeathRepository               $causeOfDeathRepo,
        EventDispatcher                      $eventDispatcher
    ) {
        parent::__construct($causeOfDeathRepo, $eventDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return RemoveCauseOfDeathRequest::class;
    }

    /**
     * @param RemoveCauseOfDeathRequest $request
     *
     * @throws \RuntimeException when the cause of death is not found
     */
    public function execute($request): void
    {
        $this->assertSupportedRequestClass($request);

        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $this->causeOfDeathRemover->remove($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathRemoved(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));
    }
}
