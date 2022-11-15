<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Query\ShowGraveSite;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ShowGraveSiteRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
    ) {}
}
