<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\CreateNaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCreated;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateNaturalPersonService extends NaturalPersonService
{
    public function __construct(
        private NaturalPersonFactory        $naturalPersonFactory,
        NaturalPersonRepository             $naturalPersonRepo,
        EventDispatcher                     $eventDispatcher,
        CreateNaturalPersonRequestValidator $requestValidator,
    ) {
        parent::__construct($naturalPersonRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        /** @var CreateNaturalPersonRequest $request */
        $naturalPerson = $this->naturalPersonFactory->create(
            $request->fullName,
            $request->phone,
            $request->phoneAdditional,
            $request->address,
            $request->email,
            $request->bornAt,
            $request->placeOfBirth,
            $request->passportSeries,
            $request->passportNumber,
            $request->passportIssuedAt,
            $request->passportIssuedBy,
            $request->passportDivisionCode,
            $request->diedAt,
            $request->age,
            $request->causeOfDeathId,
            $request->deathCertificateSeries,
            $request->deathCertificateNumber,
            $request->deathCertificateIssuedAt,
            $request->cremationCertificateNumber,
            $request->cremationCertificateIssuedAt,
        );
        $this->naturalPersonRepo->save($naturalPerson);
        $this->eventDispatcher->dispatch(new NaturalPersonCreated(
            $naturalPerson->id(),
            $naturalPerson->fullName(),
            $naturalPerson->bornAt(),
            $naturalPerson->deceasedDetails()?->diedAt(),
        ));

        return new CreateNaturalPersonResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return CreateNaturalPersonRequest::class;
    }
}
