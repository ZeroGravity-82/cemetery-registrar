<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathService extends CauseOfDeathService
{
    /**
     * {@inheritdoc}
     */
    public function supportedRequestClassName(): string
    {
        return RemoveCauseOfDeathRequest::class;
    }

    /**
     * @param RemoveCauseOfDeathRequest $request
     */
    public function execute($request): void
    {
        $this->assertSupportedRequestClass($request);

        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $this->causeOfDeathRepo->remove($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathRemoved(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));
    }
}
