<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonPassport;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonPassportClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NaturalPerson\Passport;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonPassportService extends NaturalPersonService
{
    public function __construct(
        NaturalPersonRepository                      $naturalPersonRepo,
        EventDispatcher                              $eventDispatcher,
        ClarifyNaturalPersonPassportRequestValidator $requestValidator,
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
        $isClarified = false;

        /** @var ClarifyNaturalPersonPassportRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        $passport      = $this->buildPassport($request);
        if (!$this->isSamePassport($passport, $naturalPerson)) {
            $naturalPerson->setPassport($passport);
            $isClarified = true;
        }
        if ($isClarified) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonPassportClarified(
                $naturalPerson->id(),
                $naturalPerson->passport(),
            ));
        }

        return new ClarifyNaturalPersonPassportResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyNaturalPersonPassportRequest::class;
    }

    /**
     * @throws Exception when the passport fields have invalid values
     */
    private function buildPassport(ApplicationRequest $request): Passport
    {
        /** @var ClarifyNaturalPersonPassportRequest $request */
        return new Passport(
            $request->passportSeries,
            $request->passportNumber,
            $this->buildPassportIssuedAt($request),
            $request->passportIssuedBy,
            $request->passportDivisionCode,
        );
    }

    private function buildPassportIssuedAt(ApplicationRequest $request): ?\DateTimeImmutable
    {
        /** @var ClarifyNaturalPersonPassportRequest $request */
        return $request->passportIssuedAt !== null
            ? \DateTimeImmutable::createFromFormat('Y-m-d', $request->passportIssuedAt)
            : null;
    }

    private function isSamePassport(Passport $passport, NaturalPerson $naturalPerson): bool
    {
        return $naturalPerson->passport() !== null &&
               $passport->isEqual($naturalPerson->passport());
    }
}
