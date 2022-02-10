<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\AbstractEntityFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeceasedFactory extends AbstractEntityFactory
{
    /**
     * Creates an object for the deceased.
     *
     * @param string             $naturalPersonId
     * @param \DateTimeImmutable $diedAt
     * @param string|null        $deathCertificateId
     * @param string|null        $causeOfDeath
     *
     * @return Deceased
     */
    public function create(
        string              $naturalPersonId,
        \DateTimeImmutable  $diedAt,
        ?string             $deathCertificateId,
        ?string             $causeOfDeath,
    ): Deceased {
        $nextId             = new DeceasedId($this->identityGenerator->getNextIdentity());
        $naturalPersonId    = new NaturalPersonId($naturalPersonId);
        $deathCertificateId = $deathCertificateId ? new DeathCertificateId($deathCertificateId) : null;
        $causeOfDeath       = $causeOfDeath ? new CauseOfDeath($causeOfDeath) : null;

        return new Deceased($nextId, $naturalPersonId, $diedAt, $deathCertificateId, $causeOfDeath);
    }
}
