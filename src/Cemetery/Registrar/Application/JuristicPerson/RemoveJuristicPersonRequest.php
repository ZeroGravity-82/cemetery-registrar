<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class RemoveJuristicPersonRequest
{
    /**
     * @param string $juristicPersonId
     */
    public function __construct(
        public string $juristicPersonId,
    ) {}
}
