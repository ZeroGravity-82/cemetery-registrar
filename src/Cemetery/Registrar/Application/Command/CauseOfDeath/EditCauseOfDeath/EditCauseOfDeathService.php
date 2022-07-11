<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\CauseOfDeath\EditCauseOfDeath;

use Cemetery\Registrar\Application\Command\CauseOfDeath\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathEdited;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathName;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditCauseOfDeathService extends CauseOfDeathService
{
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
        $causeOfDeath = $this->getCauseOfDeath($request->id);
        $causeOfDeath->setName(new CauseOfDeathName($request->name));
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathEdited(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new EditCauseOfDeathResponse($causeOfDeath->id()->value());
    }
}
