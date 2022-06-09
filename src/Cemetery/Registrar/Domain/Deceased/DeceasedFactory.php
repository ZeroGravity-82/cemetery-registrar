<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\EntityFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedFactory extends EntityFactory
{
    /**
     * @param string|null $naturalPersonId
     * @param string|null $diedAt
     * @param int|null    $age
     * @param string|null $deathCertificateId
     * @param string|null $causeOfDeath
     *
     * @return Deceased
     */
    public function create(
        ?string $naturalPersonId,
        ?string $diedAt,
        ?int    $age,
        ?string $deathCertificateId,
        ?string $causeOfDeath,
    ): Deceased {
        $naturalPersonId    = new NaturalPersonId((string) $naturalPersonId);
        $diedAt             = \DateTimeImmutable::createFromFormat('Y-m-d', $diedAt);
        $age                = $age !== null                ? new Age($age)                               : null;
        $deathCertificateId = $deathCertificateId !== null ? new DeathCertificateId($deathCertificateId) : null;
        $causeOfDeath       = $causeOfDeath !== null       ? new CauseOfDeath($causeOfDeath)             : null;

        return (new Deceased(
            new DeceasedId($this->identityGenerator->getNextIdentity()),
            $naturalPersonId,
            $diedAt,
        ))
            ->setAge($age)
            ->setDeathCertificateId($deathCertificateId)
            ->setCauseOfDeath($causeOfDeath);
    }
}
