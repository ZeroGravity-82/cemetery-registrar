<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathCreated;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathFactory;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCauseOfDeathService extends CauseOfDeathService
{
    public function __construct(
        private readonly CauseOfDeathFactory $causeOfDeathFactory,
        CauseOfDeathRepository               $causeOfDeathRepo,
        EventDispatcher                      $eventDispatcher,
        CreateCauseOfDeathRequestValidator   $requestValidator,
    ) {
        parent::__construct($causeOfDeathRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateCauseOfDeathRequest $request */
        $causeOfDeath = $this->causeOfDeathFactory->create(
            $request->name,
        );
        $this->causeOfDeathRepo->save($causeOfDeath);
        $this->eventDispatcher->dispatch(new CauseOfDeathCreated(
            $causeOfDeath->id(),
            $causeOfDeath->name(),
        ));

        return new CreateCauseOfDeathResponse(
            $causeOfDeath->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateCauseOfDeathRequest::class;
    }
}
