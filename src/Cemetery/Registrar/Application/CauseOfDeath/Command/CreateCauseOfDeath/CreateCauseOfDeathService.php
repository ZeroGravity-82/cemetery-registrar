<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CauseOfDeath\Command\CreateCauseOfDeath;

use Cemetery\Registrar\Application\CauseOfDeath\Command\CauseOfDeathService;
use Cemetery\Registrar\Application\Notification;
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
     * @param CreateCauseOfDeathRequestValidator $requestValidator
     * @param CauseOfDeathFactory                $causeOfDeathFactory
     * @param CauseOfDeathRepository             $causeOfDeathRepo
     * @param EventDispatcher                    $eventDispatcher
     */
    public function __construct(
        private readonly CreateCauseOfDeathRequestValidator $requestValidator,
        private readonly CauseOfDeathFactory                $causeOfDeathFactory,
        CauseOfDeathRepository                              $causeOfDeathRepo,
        EventDispatcher                                     $eventDispatcher,
    ) {
        parent::__construct($causeOfDeathRepo, $eventDispatcher);
//        $this->validator = $createCauseOfDeathValidator;
    }

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
        $this->assertSupportedRequestClass($request);     // TODO move to parent class
//        $this->requestValidator->validate($request);    // TODO move to parent class

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
