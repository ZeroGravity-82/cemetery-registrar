<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Application\NaturalPerson\Command\ClarifyNaturalPersonDeceasedDetails;

use Cemetery\Registrar\Application\ApplicationRequest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ClarifyNaturalPersonDeceasedDetailsRequest extends ApplicationRequest
{
    public function __construct(
        public ?string $id,
        public ?string $diedAt,
        public ?int    $age,
        public ?string $causeOfDeathId,
        public ?string $deathCertificateSeries,
        public ?string $deathCertificateNumber,
        public ?string $deathCertificateIssuedAt,
        public ?string $cremationCertificateNumber,
        public ?string $cremationCertificateIssuedAt,
        public ?string $bornAt,
    ) {}
}
