<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\RemoveNaturalPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\AbstractNaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRemoved;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveNaturalPersonService extends AbstractNaturalPersonService
{
    public function __construct(
        RemoveNaturalPersonRequestValidator $requestValidator,
        NaturalPersonRepositoryInterface    $naturalPersonRepo,
        EventDispatcher                     $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $naturalPersonRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the natural person is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var RemoveNaturalPersonRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        $this->naturalPersonRepo->remove($naturalPerson);
        $this->eventDispatcher->dispatch(new NaturalPersonRemoved($naturalPerson->id()));

        return new ApplicationSuccessResponse();
    }

    protected function supportedRequestClassName(): string
    {
        return RemoveNaturalPersonRequest::class;
    }
}
