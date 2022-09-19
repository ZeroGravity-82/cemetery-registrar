<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonFullName;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFullNameClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepository;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonFullNameService extends NaturalPersonService
{
    public function __construct(
        NaturalPersonRepository                      $naturalPersonRepo,
        EventDispatcher                              $eventDispatcher,
        ClarifyNaturalPersonFullNameRequestValidator $requestValidator,
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

        /** @var ClarifyNaturalPersonFullNameRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        if ($naturalPerson->fullName()->value() !== $request->fullName) {
            $naturalPerson->setFullName($this->buildFullName($request));
            $isClarified = true;
        }
        if ($isClarified) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonFullNameClarified(
                $naturalPerson->id(),
                $naturalPerson->fullName(),
            ));
        }

        return new ClarifyNaturalPersonFullNameResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyNaturalPersonFullNameRequest::class;
    }

    /**
     * @throws Exception when the full name has invalid value
     */
    private function buildFullName(ApplicationRequest $request): FullName
    {
        /** @var ClarifyNaturalPersonFullNameRequest $request */
        return new FullName($request->fullName);
    }
}
