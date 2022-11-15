<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\BurialPlace\GraveSite\Command\CreateCemeteryBlock;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateCemeteryBlockRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $name,
    ) {}
}
