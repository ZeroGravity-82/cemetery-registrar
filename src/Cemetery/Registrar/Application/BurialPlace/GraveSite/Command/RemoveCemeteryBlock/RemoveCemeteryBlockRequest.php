<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveCemeteryBlockRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
