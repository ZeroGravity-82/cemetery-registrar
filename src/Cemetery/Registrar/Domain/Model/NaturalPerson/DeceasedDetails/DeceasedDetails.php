<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetails
{
    public function __construct(
        private \DateTimeImmutable    $diedAt,
        private ?Age                  $age,
        private ?CauseOfDeathId       $causeOfDeathId,
        private ?DeathCertificate     $deathCertificate,
        private ?CremationCertificate $cremationCertificate,
    ) {}

    public function diedAt(): \DateTimeImmutable
    {
        return $this->diedAt;
    }

    public function age(): ?Age
    {
        return $this->age;
    }

    public function causeOfDeathId(): ?CauseOfDeathId
    {
        return $this->causeOfDeathId;
    }

    public function deathCertificate(): ?DeathCertificate
    {
        return $this->deathCertificate;
    }

    public function cremationCertificate(): ?CremationCertificate
    {
        return $this->cremationCertificate;
    }

    public function isEqual(self $deceasedDetails): bool
    {
        $isSameDiedAt = $deceasedDetails->diedAt->format('Y-m-d') === $this->diedAt->format('Y-m-d');
        $isSameAge    = $deceasedDetails->age() !== null && $this->age() !== null
            ? $deceasedDetails->age()->isEqual($this->age())
            : $deceasedDetails->age() === null && $this->age() === null;
        $isSameCauseOfDeathId = $deceasedDetails->causeOfDeathId() !== null && $this->causeOfDeathId() !== null
            ? $deceasedDetails->causeOfDeathId()->isEqual($this->causeOfDeathId())
            : $deceasedDetails->causeOfDeathId() === null && $this->causeOfDeathId() === null;
        $isSameDeathCertificate = $deceasedDetails->deathCertificate() !== null && $this->deathCertificate() !== null
            ? $deceasedDetails->deathCertificate()->isEqual($this->deathCertificate())
            : $deceasedDetails->deathCertificate() === null && $this->deathCertificate() === null;
        $isSameCremationCertificate = $deceasedDetails->cremationCertificate() !== null && $this->cremationCertificate() !== null
            ? $deceasedDetails->cremationCertificate()->isEqual($this->cremationCertificate())
            : $deceasedDetails->cremationCertificate() === null && $this->cremationCertificate() === null;

        return $isSameDiedAt && $isSameAge && $isSameCauseOfDeathId && $isSameDeathCertificate &&
               $isSameCremationCertificate;
    }
}
