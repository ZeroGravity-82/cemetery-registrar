<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateGraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateGraveSiteRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $cemeteryBlockId,
        public ?int    $rowInBlock,
        public ?int    $positionInRow,
        public ?string $geoPositionLatitude,
        public ?string $geoPositionLongitude,
        public ?string $geoPositionError,
        public ?string $size,
        public ?string $personInChargeId,
    ) {}
}
