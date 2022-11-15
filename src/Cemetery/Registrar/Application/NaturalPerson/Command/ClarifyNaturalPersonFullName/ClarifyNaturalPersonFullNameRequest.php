<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonFullName;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonFullNameRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $fullName,
    ) {}
}
