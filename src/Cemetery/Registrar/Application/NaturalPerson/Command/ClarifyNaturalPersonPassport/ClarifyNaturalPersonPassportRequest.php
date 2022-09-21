<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonPassport;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonPassportRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $passportSeries,
        public ?string $passportNumber,
        public ?string $passportIssuedAt,
        public ?string $passportIssuedBy,
        public ?string $passportDivisionCode,
    ) {}
}
