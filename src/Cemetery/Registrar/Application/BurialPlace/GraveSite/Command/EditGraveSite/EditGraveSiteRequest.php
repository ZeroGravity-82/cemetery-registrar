<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\EditGraveSite;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class EditGraveSiteRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $cemeteryBlockId,
        public ?int    $rowInBlock,
        public ?int    $positionInRow,
        public ?string $geoPositionLatitude,
        public ?string $geoPositionLongitude,
        public ?string $geoPositionError,
        public ?string $size,
    ) {}
}
