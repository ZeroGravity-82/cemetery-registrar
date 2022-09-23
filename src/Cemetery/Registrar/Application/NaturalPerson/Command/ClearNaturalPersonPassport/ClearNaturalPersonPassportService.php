<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClearNaturalPersonPassport;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonPassportCleared;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearNaturalPersonPassportService extends NaturalPersonService
{
    public function __construct(
        NaturalPersonRepository                    $naturalPersonRepo,
        EventDispatcher                            $eventDispatcher,
        ClearNaturalPersonPassportRequestValidator $requestValidator,
    ) {
        parent::__construct($naturalPersonRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the natural person is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $isCleared = false;

        /** @var ClearNaturalPersonPassportRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        if ($naturalPerson->passport() !== null) {
            $naturalPerson->setPassport(null);
            $isCleared = true;
        }
        if ($isCleared) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonPassportCleared(
                $naturalPerson->id(),
            ));
        }

        return new ClearNaturalPersonPassportResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClearNaturalPersonPassportRequest::class;
    }
}
