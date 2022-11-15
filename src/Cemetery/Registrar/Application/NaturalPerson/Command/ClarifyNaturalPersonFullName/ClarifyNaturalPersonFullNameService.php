<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonFullName;

use Cemetery\Registrar\Application\AbstractApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\AbstractNaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonFullNameClarified;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonFullNameService extends AbstractNaturalPersonService
{
    public function __construct(
        ClarifyNaturalPersonFullNameRequestValidator $requestValidator,
        NaturalPersonRepositoryInterface             $naturalPersonRepo,
        EventDispatcher                              $eventDispatcher,
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
        $isClarified = false;

        /** @var ClarifyNaturalPersonFullNameRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        $fullName      = $this->buildFullName($request);
        if (!$this->isSameFullName($fullName, $naturalPerson)) {
            $naturalPerson->setFullName($fullName);
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
    private function buildFullName(AbstractApplicationRequest $request): FullName
    {
        /** @var ClarifyNaturalPersonFullNameRequest $request */
        return new FullName($request->fullName);
    }

    private function isSameFullName(FullName $fullName, NaturalPerson $naturalPerson): bool
    {
        return $naturalPerson->fullName()->isEqual($fullName);
    }
}
