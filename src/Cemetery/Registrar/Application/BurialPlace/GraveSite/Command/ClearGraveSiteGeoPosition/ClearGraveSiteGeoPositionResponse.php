<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClearGraveSiteGeoPosition;

use Cemetery\Registrar\Application\ApplicationSuccessResponse;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClearGraveSiteGeoPositionResponse extends ApplicationSuccessResponse
{
    public function __construct(
        string $id,
    ) {
        $this->data = (object) [
            'id' => $id,
        ];
    }
}
