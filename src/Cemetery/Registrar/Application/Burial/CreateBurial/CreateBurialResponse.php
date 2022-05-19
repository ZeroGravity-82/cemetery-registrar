<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\CreateBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateBurialResponse
{
    public function __construct(
        public readonly string $burialId,
    ) {}
}
