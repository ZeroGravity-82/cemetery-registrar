<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\JuristicPerson\Command\JuristicPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemoved;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonService extends JuristicPersonService
{
    public function __construct(
        JuristicPersonRepository             $juristicPersonRepo,
        EventDispatcher                      $eventDispatcher,
        RemoveJuristicPersonRequestValidator $requestValidator,
    ) {
        parent::__construct($juristicPersonRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the juristic person is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var RemoveJuristicPersonRequest $request */
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRepo->remove($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonRemoved(
            $juristicPerson->id(),
            $juristicPerson->name(),
            $juristicPerson->inn(),
        ));

        return new ApplicationSuccessResponse();
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveJuristicPersonRequest::class;
    }
}
