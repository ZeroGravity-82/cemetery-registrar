<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonFullName;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonFullNameRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $fullName,
    ) {}
}
