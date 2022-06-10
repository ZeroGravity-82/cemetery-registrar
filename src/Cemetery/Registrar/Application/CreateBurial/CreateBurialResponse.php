<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\CreateBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateBurialResponse
{
    public function __construct(
        public readonly string $burialId,
    ) {}
}
