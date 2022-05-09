<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\EntityFactory;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeceasedFactory extends EntityFactory
{
    /**
     * @param string      $naturalPersonId
     * @param string      $diedAt
     * @param string|null $deathCertificateId
     * @param string|null $causeOfDeath
     *
     * @return Deceased
     */
    public function create(
        string  $naturalPersonId,
        string  $diedAt,
        ?string $deathCertificateId,
        ?string $causeOfDeath,
    ): Deceased {
        $naturalPersonId    = new NaturalPersonId($naturalPersonId);
        $diedAt             = \DateTimeImmutable::createFromFormat('Y-m-d', $diedAt);
        $deathCertificateId = $deathCertificateId !== null ? new DeathCertificateId($deathCertificateId) : null;
        $causeOfDeath       = $causeOfDeath !== null       ? new CauseOfDeath($causeOfDeath)             : null;

        return (new Deceased(
                new DeceasedId($this->identityGenerator->getNextIdentity()),
                $naturalPersonId,
                $diedAt,
            ))
            ->setDeathCertificateId($deathCertificateId)
            ->setCauseOfDeath($causeOfDeath);
    }
}
