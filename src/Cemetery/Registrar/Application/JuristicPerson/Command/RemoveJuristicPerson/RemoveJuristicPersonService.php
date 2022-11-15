<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson\Command\RemoveJuristicPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\JuristicPerson\Command\AbstractJuristicPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRemoved;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveJuristicPersonService extends AbstractJuristicPersonService
{
    public function __construct(
        RemoveJuristicPersonRequestValidator $requestValidator,
        JuristicPersonRepositoryInterface    $juristicPersonRepo,
        EventDispatcher                      $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $juristicPersonRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the juristic person is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var RemoveJuristicPersonRequest $request */
        $juristicPerson = $this->getJuristicPerson($request->id);
        $this->juristicPersonRepo->remove($juristicPerson);
        $this->eventDispatcher->dispatch(new JuristicPersonRemoved($juristicPerson->id()));

        return new ApplicationSuccessResponse();
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveJuristicPersonRequest::class;
    }
}
