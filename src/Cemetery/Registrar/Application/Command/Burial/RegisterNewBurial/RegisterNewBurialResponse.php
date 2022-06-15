<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\Burial\RegisterNewBurial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RegisterNewBurialResponse
{
    public function __construct(
        public readonly string $burialId,
    ) {}
}