<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Burial\Command\RegisterNewBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RegisterNewBurialResponse
{
    public function __construct(
        public readonly string $id,
    ) {}
}
