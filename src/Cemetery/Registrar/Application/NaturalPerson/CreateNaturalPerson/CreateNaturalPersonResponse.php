<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\CreateNaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class CreateNaturalPersonResponse
{
    /**
     * @param string $naturalPersonId
     */
    public function __construct(
        public string $naturalPersonId,
    ) {}
}
