<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateSoleProprietorResponse
{
    /**
     * @param string $id
     */
    public function __construct(
        public readonly string $id,
    ) {}
}
