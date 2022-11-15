<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteGeoPosition;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteGeoPositionRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $geoPositionLatitude,
        public ?string $geoPositionLongitude,
        public ?string $geoPositionError,
    ) {}
}
