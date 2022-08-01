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

    public function isEqual(self $bankDetails): bool
    {
        $isSameDiedAt = $bankDetails->diedAt->format('Y-m-d') === $this->diedAt->format('Y-m-d');
        $isSameAge    = $bankDetails->age() !== null && $this->age() !== null
            ? $bankDetails->age()->isEqual($this->age())
            : $bankDetails->age() === null && $this->age() === null;
        $isSameCauseOfDeathId = $bankDetails->causeOfDeathId() !== null && $this->causeOfDeathId() !== null
            ? $bankDetails->causeOfDeathId()->isEqual($this->causeOfDeathId())
            : $bankDetails->causeOfDeathId() === null && $this->causeOfDeathId() === null;
        $isSameDeathCertificate = $bankDetails->deathCertificate() !== null && $this->deathCertificate() !== null
            ? $bankDetails->deathCertificate()->isEqual($this->deathCertificate())
            : $bankDetails->deathCertificate() === null && $this->deathCertificate() === null;
        $isSameCremationCertificate = $bankDetails->cremationCertificate() !== null && $this->cremationCertificate() !== null
            ? $bankDetails->cremationCertificate()->isEqual($this->cremationCertificate())
            : $bankDetails->cremationCertificate() === null && $this->cremationCertificate() === null;

        return $isSameDiedAt && $isSameAge && $isSameCauseOfDeathId && $isSameDeathCertificate &&
               $isSameCremationCertificate;
    }
}
