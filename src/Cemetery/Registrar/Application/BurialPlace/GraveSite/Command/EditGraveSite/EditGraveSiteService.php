<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;
use Cemetery\Registrar\Application\ApplicationSuccessResponse;
use Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\GraveSiteService;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteEdited;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRepository;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Model\EventDispatcher;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditGraveSiteService extends GraveSiteService
{
    public function __construct(
        GraveSiteRepository           $graveSiteRepo,
        CemeteryBlockRepository       $cemeteryBlockRepo,
        EventDispatcher               $eventDispatcher,
        EditGraveSiteRequestValidator $requestValidator,
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
        /** @var EditGraveSiteRequest $request */
        $graveSite = $this->getGraveSite($request->id);
        $graveSite->assignCemeteryBlock($this->buildCemeteryBlockId($request));
        $graveSite->setRowInBlock($this->buildRowInBlocks($request));
        $graveSite->setPositionInRow($this->buildPositionInRow($request));
        $graveSite->setGeoPosition($this->buildGeoPosition($request));
        $graveSite->setSize($this->buildSize($request));
        $this->graveSiteRepo->save($graveSite);
        $this->eventDispatcher->dispatch(new GraveSiteEdited(
            $graveSite->id(),
            $graveSite->cemeteryBlockId(),
            $graveSite->rowInBlock(),
            $graveSite->positionInRow(),
        ));

        return new EditGraveSiteResponse(
            $graveSite->id()->value(),
        );
    }

    protected function supportedRequestClassName(): string
    {
        return EditGraveSiteRequest::class;
    }

    /**
     * @throws Exception when the cemetery block ID has invalid value
     */
    private function buildCemeteryBlockId(ApplicationRequest $request): CemeteryBlockId
    {
        /** @var EditGraveSiteRequest $request */
        return new CemeteryBlockId($request->cemeteryBlockId);
    }

    /**
     * @throws Exception when the row in block has invalid value
     */
    private function buildRowInBlocks(ApplicationRequest $request): RowInBlock
    {
        /** @var EditGraveSiteRequest $request */
        return new RowInBlock($request->rowInBlock);
    }

    /**
     * @throws Exception when the position in row has invalid value
     */
    private function buildPositionInRow(ApplicationRequest $request): ?PositionInRow
    {
        /** @var EditGraveSiteRequest $request */
        return $request->positionInRow ? new PositionInRow($request->positionInRow) : null;
    }

    /**
     * @throws Exception when the geo position latitude, longitude or error have invalid value
     */
    private function buildGeoPosition(ApplicationRequest $request): ?GeoPosition
    {
        /** @var EditGraveSiteRequest $request */
        if ($request->geoPositionLatitude === null && $request->geoPositionLongitude === null) {
            return null;
        }
        /** @var EditGraveSiteRequest $request */
        return new GeoPosition(
            new Coordinates($request->geoPositionLatitude, $request->geoPositionLongitude),
            $request->geoPositionError !== null ? new Error($request->geoPositionError) : null,
        );
    }

    /**
     * @throws Exception when the size has invalid value
     */
    private function buildSize(ApplicationRequest $request): ?GraveSiteSize
    {
        /** @var EditGraveSiteRequest $request */
        return $request->size ? new GraveSiteSize($request->size) : null;
    }
}
