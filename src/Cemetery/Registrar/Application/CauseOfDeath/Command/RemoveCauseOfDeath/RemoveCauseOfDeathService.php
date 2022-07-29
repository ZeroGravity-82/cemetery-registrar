<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\RemoveCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRemoved;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCauseOfDeathService extends CauseOfDeathService
{
    /**
     * @param RemoveCauseOfDeathRequest $request
     *
     * @return ApplicationResponseSuccess
     *
     * @throws \RuntimeException when the cause of death is not found
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        $this->assertSupportedRequestClass($request);

        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $this->causeOfDeathRepo->remove($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathRemoved(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function supportedRequestClassName(): string
    {
        return RemoveCauseOfDeathRequest::class;
    }
}
