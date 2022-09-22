<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonView
{
    public function __construct(
        public string  $id,
        public string  $fullName,
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
        public ?string $causeOfDeathName,
        public ?string $deathCertificateSeries,
        public ?string $deathCertificateNumber,
        public ?string $deathCertificateIssuedAt,
        public ?string $cremationCertificateNumber,
        public ?string $cremationCertificateIssuedAt,
        public string  $createdAt,
        public string  $updatedAt,
    ) {}
}
