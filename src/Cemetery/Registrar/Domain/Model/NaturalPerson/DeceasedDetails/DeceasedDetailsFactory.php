<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsFactory
{
    /**
     * @param string|null $naturalPersonId
     * @param string|null $diedAt
     * @param int|null    $age
     * @param string|null $deathCertificateId
     * @param string|null $causeOfDeathId
     *
     * @return Deceased
     */
    public function create(
        ?string $naturalPersonId,
        ?string $diedAt,
        ?int    $age,
        ?string $deathCertificateId,
        ?string $causeOfDeathId,
    ): Deceased {
        $naturalPersonId    = new NaturalPersonId((string) $naturalPersonId);
        $diedAt             = \DateTimeImmutable::createFromFormat('Y-m-d', $diedAt);
        $age                = $age                !== null ? new Age($age)                               : null;
        $deathCertificateId = $deathCertificateId !== null ? new DeathCertificateId($deathCertificateId) : null;
        $causeOfDeathId     = $causeOfDeathId     !== null ? new CauseOfDeathId($causeOfDeathId)         : null;

        return (new Deceased(
            new DeceasedId($this->identityGenerator->getNextIdentity()),
            $naturalPersonId,
            $diedAt,
        ))
            ->setAge($age)
            ->setDeathCertificateId($deathCertificateId)
            ->setCauseOfDeathId($causeOfDeathId);
    }
}
