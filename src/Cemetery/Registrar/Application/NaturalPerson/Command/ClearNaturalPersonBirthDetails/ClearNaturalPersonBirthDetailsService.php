<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClearNaturalPersonBirthDetails;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonBirthDetailsCleared;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearNaturalPersonBirthDetailsService extends NaturalPersonService
{
    public function __construct(
        ClearNaturalPersonBirthDetailsRequestValidator $requestValidator,
        NaturalPersonRepositoryInterface               $naturalPersonRepo,
        EventDispatcher                                $eventDispatcher,
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

        /** @var ClearNaturalPersonBirthDetailsRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        if ($naturalPerson->bornAt() !== null) {
            $naturalPerson->setBornAt(null);
            $isCleared = true;
        }
        if ($naturalPerson->placeOfBirth() !== null) {
            $naturalPerson->setPlaceOfBirth(null);
            $isCleared = true;
        }
        if ($isCleared) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonBirthDetailsCleared(
                $naturalPerson->id(),
            ));
        }

        return new ClearNaturalPersonBirthDetailsResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClearNaturalPersonBirthDetailsRequest::class;
    }
}
