<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\ClarifyGraveSiteLocation;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyGraveSiteLocationRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $cemeteryBlockId,
        public ?int    $rowInBlock,
        public ?int    $positionInRow,
    ) {}
}
