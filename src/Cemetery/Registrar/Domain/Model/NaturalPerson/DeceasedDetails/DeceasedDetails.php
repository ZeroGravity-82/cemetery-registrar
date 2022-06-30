<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetails
{
    /**
     * @param NaturalPersonId           $naturalPersonId
     * @param \DateTimeImmutable        $diedAt
     * @param Age|null                  $age
     * @param CauseOfDeathId|null       $causeOfDeathId
     * @param DeathCertificate|null     $deathCertificate
     * @param CremationCertificate|null $cremationCertificate
     */
    public function __construct(
        private readonly NaturalPersonId       $naturalPersonId,
        private readonly \DateTimeImmutable    $diedAt,
        private readonly ?Age                  $age,
        private readonly ?CauseOfDeathId       $causeOfDeathId,
        private readonly ?DeathCertificate     $deathCertificate,
        private readonly ?CremationCertificate $cremationCertificate,
    ) {
        // $this->assert();
    }

    /**
     * @return NaturalPersonId
     */
    public function naturalPersonId(): NaturalPersonId
    {
        return $this->naturalPersonId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function diedAt(): \DateTimeImmutable
    {
        return $this->diedAt;
    }

    /**
     * @return Age|null
     */
    public function age(): ?Age
    {
        return $this->age;
    }

    /**
     * @return CauseOfDeathId|null
     */
    public function causeOfDeathId(): ?CauseOfDeathId
    {
        return $this->causeOfDeathId;
    }

    /**
     * @return DeathCertificate|null
     */
    public function deathCertificate(): ?DeathCertificate
    {
        return $this->deathCertificate;
    }

    /**
     * @return CremationCertificate|null
     */
    public function cremationCertificate(): ?CremationCertificate
    {
        return $this->cremationCertificate;
    }
}
