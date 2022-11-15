<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonDeceasedDetails;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\Age;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\CremationCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeathCertificate;
use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonDeceasedDetailsClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonDeceasedDetailsService extends NaturalPersonService
{
    public function __construct(
        ClarifyNaturalPersonDeceasedDetailsRequestValidator $requestValidator,
        NaturalPersonRepositoryInterface                    $naturalPersonRepo,
        EventDispatcher                                     $eventDispatcher,
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
        $isClarified = false;

        /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        $naturalPerson   = $this->getNaturalPerson($request->id);
        $deceasedDetails = $this->buildDeceasedDetails($request);
        if (!$this->isSameDeceasedDetails($deceasedDetails, $naturalPerson)) {
            $naturalPerson->setDeceasedDetails($deceasedDetails);
            $isClarified = true;
        }
        if ($isClarified) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonDeceasedDetailsClarified(
                $naturalPerson->id(),
                $naturalPerson->deceasedDetails(),
            ));
        }

        return new ClarifyNaturalPersonDeceasedDetailsResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyNaturalPersonDeceasedDetailsRequest::class;
    }

    /**
     * @throws Exception when the deceased details fields have invalid values
     */
    private function buildDeceasedDetails(ApplicationRequest $request): DeceasedDetails
    {
        /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return new DeceasedDetails(
            $this->buildDiedAt($request),
            $this->buildAge($request),
            $this->buildCauseOfDeathId($request),
            $this->buildDeathCertificate($request),
            $this->buildCremationCertificate($request),
        );
    }

    private function isSameDeceasedDetails(DeceasedDetails $deceasedDetails, NaturalPerson $naturalPerson): bool
    {
        return $naturalPerson->deceasedDetails() !== null &&
               $deceasedDetails->isEqual($naturalPerson->deceasedDetails());
    }

    private function buildDiedAt(ApplicationRequest $request): \DateTimeImmutable
    {
        /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return \DateTimeImmutable::createFromFormat('Y-m-d', $request->diedAt);
    }

    /**
     * @throws Exception when the age field has invalid value
     */
    private function buildAge(ApplicationRequest $request): ?Age
    {
         /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return $request->age !== null
            ? new Age($request->age)
            : null;
    }

    /**
     * @throws Exception when the cause of death ID field has invalid value
     */
    private function buildCauseOfDeathId(ApplicationRequest $request): ?CauseOfDeathId
    {
         /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return $request->causeOfDeathId !== null
            ? new CauseOfDeathId($request->causeOfDeathId)
            : null;
    }

    /**
     * @throws Exception when the death certificate fields have invalid values
     */
    private function buildDeathCertificate(ApplicationRequest $request): ?DeathCertificate
    {
         /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return $request->deathCertificateSeries   !== null ||
               $request->deathCertificateNumber   !== null ||
               $request->deathCertificateIssuedAt !== null
            ? new DeathCertificate(
                $request->deathCertificateSeries,
                $request->deathCertificateNumber,
                $this->buildDeathCertificateIssuedAt($request),
            )
            : null;
    }

    private function buildDeathCertificateIssuedAt(ApplicationRequest $request): ?\DateTimeImmutable
    {
        /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return $request->deathCertificateIssuedAt !== null
            ? \DateTimeImmutable::createFromFormat('Y-m-d', $request->deathCertificateIssuedAt)
            : null;
    }

    /**
     * @throws Exception when the cremation certificate fields have invalid values
     */
    private function buildCremationCertificate(ApplicationRequest $request): ?CremationCertificate
    {
         /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return $request->cremationCertificateNumber   !== null ||
               $request->cremationCertificateIssuedAt !== null
            ? new CremationCertificate(
                $request->cremationCertificateNumber,
                $this->buildCremationCertificateIssuedAt($request),
            )
            : null;
    }

    private function buildCremationCertificateIssuedAt(ApplicationRequest $request): ?\DateTimeImmutable
    {
        /** @var ClarifyNaturalPersonDeceasedDetailsRequest $request */
        return $request->cremationCertificateIssuedAt !== null
            ? \DateTimeImmutable::createFromFormat('Y-m-d', $request->cremationCertificateIssuedAt)
            : null;
    }
}
