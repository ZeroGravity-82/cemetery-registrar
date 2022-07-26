<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\EditCauseOfDeath;

use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Application\Notification;
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
     *
     * @throws \RuntimeException when the cause of death is not found
     */
    public function execute($request): EditCauseOfDeathResponse
    {
        $this->assertSupportedRequestClass($request);   // TODO move to parent::execute()
        // TODO validate request DTO

        $causeOfDeath = $this->getCauseOfDeath($request->id);

        try {
            $causeOfDeath->setName(new CauseOfDeathName($request->name));
            // ...
        } catch (DomainException $e) {
            return (new Notification())->addError(
                \get_class($e),
                $e->getMessage(),
                $e,
            );
        }

        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathEdited(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new EditCauseOfDeathResponse($causeOfDeath->id()->value());
    }
}
