<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\CreateNaturalPerson;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\AbstractNaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonCreated;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFactory;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateNaturalPersonService extends AbstractNaturalPersonService
{
    public function __construct(
        CreateNaturalPersonRequestValidator $requestValidator,
        NaturalPersonRepositoryInterface    $naturalPersonRepo,
        EventDispatcher                     $eventDispatcher,
        private NaturalPersonFactory        $naturalPersonFactory,
    ) {
        parent::__construct($requestValidator, $naturalPersonRepo, $eventDispatcher);
    }

    /**
     * @throws Exception  when there was any issue within the domain
     * @throws \Throwable when any error occurred while processing the request
     */
    public function execute(AbstractApplicationRequest $request): ApplicationSuccessResponse
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
