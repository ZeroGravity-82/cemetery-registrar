<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\CreateNaturalPerson;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CreateNaturalPersonRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $fullName,
        public ?string $phone,
        public ?string $phoneAdditional,
        public ?string $address,
        public ?string $email,
        public ?string $bornAt,
        public ?string $placeOfBirth,
        public ?string $passportSeries,
        public ?string $passportNumber,
        public ?string $passportIssuedAt,
        public ?string $passportIssuedBy,
        public ?string $passportDivisionCode,
        public ?string $diedAt,
        public ?int    $age,
        public ?string $causeOfDeathId,
        public ?string $deathCertificateSeries,
        public ?string $deathCertificateNumber,
        public ?string $deathCertificateIssuedAt,
        public ?string $cremationCertificateNumber,
        public ?string $cremationCertificateIssuedAt,
    ) {}
}
