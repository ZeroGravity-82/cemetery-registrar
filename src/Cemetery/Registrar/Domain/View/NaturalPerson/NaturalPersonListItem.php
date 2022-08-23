<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\View\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonListItem
{
    public function __construct(
        public string  $id,
        public string  $fullName,
        public ?string $address,
        public ?string $phone,
        public ?string $email,
        public ?string $bornAt,
        public ?string $placeOfBirth,
        public ?string $passport,
        public ?string $diedAt,
        public ?int    $age,
        public ?string $causeOfDeathName,
        public ?string $deathCertificate,
        public ?string $cremationCertificate,
    ) {}
}
