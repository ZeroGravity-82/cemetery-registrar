<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\RemoveGraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RemoveGraveSiteRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
