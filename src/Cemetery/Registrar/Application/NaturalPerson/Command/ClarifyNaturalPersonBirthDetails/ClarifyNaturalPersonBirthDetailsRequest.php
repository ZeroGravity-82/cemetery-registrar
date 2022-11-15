<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonBirthDetails;

use Cemetery\Registrar\Application\AbstractApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonBirthDetailsRequest extends AbstractApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $bornAt,
        public ?string $placeOfBirth,
        public ?string $diedAt,
        public ?int    $age,
    ) {}
}
