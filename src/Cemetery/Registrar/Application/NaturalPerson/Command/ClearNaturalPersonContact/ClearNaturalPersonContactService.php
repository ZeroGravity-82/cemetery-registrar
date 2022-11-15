<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClearNaturalPersonContact;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonContactCleared;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearNaturalPersonContactService extends NaturalPersonService
{
    public function __construct(
        ClearNaturalPersonContactRequestValidator $requestValidator,
        NaturalPersonRepository                   $naturalPersonRepo,
        EventDispatcher                           $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $naturalPersonRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the natural person is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $isCleared = false;

        /** @var ClearNaturalPersonContactRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        if ($naturalPerson->phone() !== null) {
            $naturalPerson->setPhone(null);
            $isCleared = true;
        }
        if ($naturalPerson->phoneAdditional() !== null) {
            $naturalPerson->setPhoneAdditional(null);
            $isCleared = true;
        }
        if ($naturalPerson->address() !== null) {
            $naturalPerson->setAddress(null);
            $isCleared = true;
        }
        if ($naturalPerson->email() !== null) {
            $naturalPerson->setEmail(null);
            $isCleared = true;
        }
        if ($isCleared) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonContactCleared(
                $naturalPerson->id(),
            ));
        }

        return new ClearNaturalPersonContactResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClearNaturalPersonContactRequest::class;
    }
}
