<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveCemeteryBlock;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCemeteryBlockRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
