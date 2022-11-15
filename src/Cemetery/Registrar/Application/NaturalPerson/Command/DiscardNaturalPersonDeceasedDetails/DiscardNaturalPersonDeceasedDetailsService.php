<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\DiscardNaturalPersonDeceasedDetails;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\NaturalPerson\Command\NaturalPersonService;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonDeceasedDetailsDiscarded;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DiscardNaturalPersonDeceasedDetailsService extends NaturalPersonService
{
    public function __construct(
        DiscardNaturalPersonDeceasedDetailsRequestValidator $requestValidator,
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
        $isDiscarded = false;

        /** @var DiscardNaturalPersonDeceasedDetailsRequest $request */
        $naturalPerson = $this->getNaturalPerson($request->id);
        if ($naturalPerson->deceasedDetails() !== null) {
            $naturalPerson->setDeceasedDetails(null);
            $isDiscarded = true;
        }
        if ($isDiscarded) {
            $this->naturalPersonRepo->save($naturalPerson);
            $this->eventDispatcher->dispatch(new NaturalPersonDeceasedDetailsDiscarded(
                $naturalPerson->id(),
            ));
        }

        return new DiscardNaturalPersonDeceasedDetailsResponse(
            $naturalPerson->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return DiscardNaturalPersonDeceasedDetailsRequest::class;
    }
}
