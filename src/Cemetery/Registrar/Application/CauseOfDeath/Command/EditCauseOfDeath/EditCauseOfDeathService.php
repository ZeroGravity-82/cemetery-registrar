<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationResponseSuccess;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathService extends CauseOfDeathService
{
    /**
     * @param EditCauseOfDeathRequest $request
     *
     * @return ApplicationResponseSuccess
     *
     * @throws \RuntimeException when the cause of death is not found
     */
    public function execute(ApplicationRequest $request): ApplicationResponseSuccess
    {
        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $causeOfDeath->setName(new CauseOfDeathName($request->name));
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathEdited(
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
        return EditCauseOfDeathRequest::class;
    }
}
