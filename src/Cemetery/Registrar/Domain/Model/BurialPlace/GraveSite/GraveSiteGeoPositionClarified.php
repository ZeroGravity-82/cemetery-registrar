<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Event;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteGeoPositionClarified extends Event
{
    public function __construct(
        private GraveSiteId $graveSiteId,
        private GeoPosition $geoPosition,
    ) {
        parent::__construct();
    }

    public function graveSiteId(): GraveSiteId
    {
        return $this->graveSiteId;
    }

    public function geoPosition(): GeoPosition
    {
        return $this->geoPosition;
    }
}