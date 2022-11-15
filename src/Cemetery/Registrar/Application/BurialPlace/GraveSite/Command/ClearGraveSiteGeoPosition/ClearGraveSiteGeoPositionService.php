<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteGeoPosition;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteGeoPositionCleared;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearGraveSiteGeoPositionService extends GraveSiteService
{
    public function __construct(
        ClearGraveSiteGeoPositionRequestValidator $requestValidator,
        GraveSiteRepository                       $graveSiteRepo,
        CemeteryBlockRepository                   $cemeteryBlockRepo,
        EventDispatcher                           $eventDispatcher,
    ) {
        parent::__construct($requestValidator, $graveSiteRepo, $cemeteryBlockRepo, $eventDispatcher);
    }

    /**
     * @throws NotFoundException when the grave site is not found
     * @throws Exception         when there was any issue within the domain
     * @throws \Throwable        when any error occurred while processing the request
     */
    public function execute(ApplicationRequest $request): ApplicationSuccessResponse
    {
        $isCleared = false;

        /** @var ClearGraveSiteGeoPositionRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if ($graveSite->geoPosition() !== null) {
            $graveSite->setGeoPosition(null);
            $isCleared = true;
        }
        if ($isCleared) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSiteGeoPositionCleared(
                $graveSite->id(),
            ));
        }

        return new ClearGraveSiteGeoPositionResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClearGraveSiteGeoPositionRequest::class;
    }
}
