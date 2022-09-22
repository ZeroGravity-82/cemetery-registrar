<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonBirthDetails;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonBirthDetailsClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NaturalPerson\PlaceOfBirth;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonBirthDetailsService extends NaturalPersonService
{
    public function __construct(
        NaturalPersonRepository                          $naturalPersonRepo,
        EventDispatcher                                  $eventDispatcher,
        ClarifyNaturalPersonBirthDetailsRequestValidator $requestValidator,
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

        /** @var ClarifyNaturalPersonBirthDetailsRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        $bornAt        = $this->buildBornAt($request);
        if (!$this->isSameBornAt($bornAt, $naturalPerson)) {
            $naturalPerson->setBornAt($bornAt);
            $isClarified = true;
        }
        $placeOfBirth = $this->buildPlaceOfBirth($request);
        if (!$this->isSamePlaceOfBirth($placeOfBirth, $naturalPerson)) {
            $naturalPerson->setPlaceOfBirth($placeOfBirth);
            $isClarified = true;
        }
        if ($isClarified) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonBirthDetailsClarified(
                $naturalPerson->id(),
                $naturalPerson->bornAt(),
                $naturalPerson->placeOfBirth(),
            ));
        }

        return new ClarifyNaturalPersonBirthDetailsResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyNaturalPersonBirthDetailsRequest::class;
    }

    private function buildBornAt(ApplicationRequest $request): ?\DateTimeImmutable
    {
        /** @var ClarifyNaturalPersonBirthDetailsRequest $request */
        return $request->bornAt !== null ? \DateTimeImmutable::createFromFormat('Y-m-d', $request->bornAt) : null;
    }

    private function isSameBornAt(?\DateTimeImmutable $bornAt, NaturalPerson $naturalPerson): bool
    {
        return $bornAt === $naturalPerson->bornAt();
    }

    /**
     * @throws Exception when the place of birth has invalid value
     */
    private function buildPlaceOfBirth(ApplicationRequest $request): ?PlaceOfBirth
    {
        /** @var ClarifyNaturalPersonBirthDetailsRequest $request */
        return $request->placeOfBirth !== null ? new PlaceOfBirth($request->placeOfBirth) : null;
    }

    private function isSamePlaceOfBirth(?PlaceOfBirth $placeOfBirth, NaturalPerson $naturalPerson): bool
    {
        return $placeOfBirth !== null && $naturalPerson->placeOfBirth() !== null
            ? $placeOfBirth->isEqual($naturalPerson->placeOfBirth())
            : $placeOfBirth === null && $naturalPerson->placeOfBirth() === null;
    }
}
