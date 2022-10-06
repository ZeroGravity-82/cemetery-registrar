<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\DiscardGraveSitePersonInCharge;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSitePersonInChargeDiscarded;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DiscardGraveSitePersonInChargeService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository                            $graveSiteRepo,
        CemeteryBlockRepository                        $cemeteryBlockRepo,
        EventDispatcher                                $eventDispatcher,
        DiscardGraveSitePersonInChargeRequestValidator $requestValidator,
    ) {
        parent::__construct($graveSiteRepo, $cemeteryBlockRepo, $eventDispatcher, $requestValidator);
    }

    /**
     * @throws NotFoundException when the grave site is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $isDiscarded = false;

        /** @var DiscardGraveSitePersonInChargeRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if ($graveSite->personInChargeId() !== null) {
            $graveSite->setPersonInCharge(null);
            $isDiscarded = true;
        }
        if ($isDiscarded) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSitePersonInChargeDiscarded(
                $graveSite->id(),
            ));
        }

        return new DiscardGraveSitePersonInChargeResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return DiscardGraveSitePersonInChargeRequest::class;
    }
}