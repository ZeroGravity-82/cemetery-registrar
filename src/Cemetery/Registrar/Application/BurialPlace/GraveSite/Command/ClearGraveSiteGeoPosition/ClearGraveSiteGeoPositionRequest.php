<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteGeoPosition;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearGraveSiteGeoPositionRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
