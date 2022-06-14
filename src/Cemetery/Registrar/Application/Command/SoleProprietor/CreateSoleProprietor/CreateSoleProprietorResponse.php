<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\Command\SoleProprietor\CreateSoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateSoleProprietorResponse
{
    public function __construct(
        public readonly string $soleProprietorId,
    ) {}
}
