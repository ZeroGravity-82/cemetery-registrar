<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteGeoPosition;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteGeoPositionClarified;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteGeoPositionService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository                         $graveSiteRepo,
        CemeteryBlockRepository                     $cemeteryBlockRepo,
        EventDispatcher                             $eventDispatcher,
        ClarifyGraveSiteGeoPositionRequestValidator $requestValidator,
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
        $isClarified = false;

        /** @var ClarifyGraveSiteGeoPositionRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        if (
            $graveSite->geoPosition()?->coordinates()->latitude()  !== $request->geoPositionLatitude  ||
            $graveSite->geoPosition()?->coordinates()->longitude() !== $request->geoPositionLongitude ||
            $graveSite->geoPosition()?->error()->value()           !== $request->geoPositionError
        ) {
            $graveSite->setGeoPosition($this->buildGeoPosition($request));
            $isClarified = true;
        }
        if ($isClarified) {
            $this->graveSiteRepo->save($graveSite);
            $this->eventDispatcher->dispatch(new GraveSiteGeoPositionClarified(
                $graveSite->id(),
                $graveSite->geoPosition(),
            ));
        }

        return new ClarifyGraveSiteGeoPositionResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return ClarifyGraveSiteGeoPositionRequest::class;
    }

    /**
     * @throws Exception when the geo position components have invalid value
     */
    private function buildGeoPosition(ApplicationRequest $request): GeoPosition
    {
        /** @var ClarifyGraveSiteGeoPositionRequest $request */
        $coordinates = new Coordinates($request->geoPositionLatitude, $request->geoPositionLongitude);
        $error       = $request->geoPositionError ? new Error($request->geoPositionError) : null;

        return new GeoPosition($coordinates, $error);
    }
}
