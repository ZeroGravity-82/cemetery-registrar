<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\EntityFactory;
use Cemetery\Registrar\Domain\Model\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonRepositoryInterface;
use Cemetery\Registrar\Domain\Model\NotFoundException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteFactory extends EntityFactory
{
    public function __construct(
        IdentityGeneratorInterface               $identityGenerator,
        private NaturalPersonRepositoryInterface $naturalPersonRepo,
    ) {
        parent::__construct($identityGenerator);
    }

    /**
     * @throws Exception         when generating an invalid grave site ID
     * @throws Exception         when the cemetery block ID is empty
     * @throws Exception         when the row in block value is invalid
     * @throws Exception         when the position in row value (if any) is invalid
     * @throws Exception         when the geo position latitude value (if any) is invalid
     * @throws Exception         when the geo position longitude value (if any) is invalid
     * @throws Exception         when the geo position error value (if any) is invalid
     * @throws Exception         when the grave site size value (if any) is invalid
     * @throws Exception         when the person in charge ID (if any) is invalid
     * @throws NotFoundException when the person in charge is not found by the ID
     */
    public function create(
        ?string $cemeteryBlockId,
        ?int    $rowInBlock,
        ?int    $positionInRow,
        ?string $geoPositionLatitude,
        ?string $geoPositionLongitude,
        ?string $geoPositionError,
        ?string $size,
        ?string $personInChargeId,
    ): GraveSite {
        $cemeteryBlockId = new CemeteryBlockId($cemeteryBlockId);
        $rowInBlock      = new RowInBlock($rowInBlock);
        $positionInRow   = $positionInRow !== null
            ? new PositionInRow($positionInRow)
            : null;
        $geoPositionCoordinates = $geoPositionLatitude !== null && $geoPositionLongitude !== null
            ? new Coordinates($geoPositionLatitude, $geoPositionLongitude)
            : null;
        $geoPositionError = $geoPositionError !== null
            ? new Error($geoPositionError)
            : null;
        $geoPosition = $geoPositionCoordinates !== null
            ? new GeoPosition($geoPositionCoordinates, $geoPositionError)
            : null;
        $size = $size !== null ? new GraveSiteSize($size) : null;

        /** @var NaturalPerson $personInCharge */
        $personInCharge = null;
        if ($personInChargeId !== null) {
            $personInCharge = $this->naturalPersonRepo->findById(new NaturalPersonId($personInChargeId));
            if ($personInCharge === null) {
                throw new NotFoundException(\sprintf('Физлицо с ID "%s" не найдено.', $personInChargeId));
            }
        }

        $graveSite = (new GraveSite(
            new GraveSiteId($this->identityGenerator->getNextIdentity()),
            $cemeteryBlockId,
            $rowInBlock,
        ))
            ->setPositionInRow($positionInRow)
            ->setGeoPosition($geoPosition)
            ->setSize($size);
        if ($personInCharge !== null) {
            $graveSite->assignPersonInCharge($personInCharge);
        }

        return $graveSite;
    }
}
