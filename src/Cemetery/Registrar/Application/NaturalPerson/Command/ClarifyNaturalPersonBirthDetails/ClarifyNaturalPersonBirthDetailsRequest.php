<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonBirthDetails;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonBirthDetailsRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $bornAt,
        public ?string $placeOfBirth,
    ) {}
}
