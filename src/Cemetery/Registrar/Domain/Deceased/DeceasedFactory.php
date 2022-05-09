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
     * @param string|null $naturalPersonId
     * @param string|null $diedAt
     * @param string|null $deathCertificateId
     * @param string|null $causeOfDeath
     *
     * @return Deceased
     */
    public function create(
        ?string $naturalPersonId,
        ?string $diedAt,
        ?string $deathCertificateId,
        ?string $causeOfDeath,
    ): Deceased {
        $this->assertNaturalPersonIdIsProvided($naturalPersonId);
        $this->assertDiedAtIsProvided($diedAt);
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

    /**
     * @param string|null $naturalPersonId
     *
     * @throws \RuntimeException when the natural person ID is not provided
     */
    private function assertNaturalPersonIdIsProvided(?string $naturalPersonId): void
    {
        if ($naturalPersonId === null) {
            throw new \RuntimeException('Идентификатор физического лица не указан.');
        }
    }

    /**
     * @param string|null $diedAt
     *
     * @throws \RuntimeException when the date of death is not provided
     */
    private function assertDiedAtIsProvided(?string $diedAt): void
    {
        if ($diedAt === null) {
            throw new \RuntimeException('Дата смерти не указана.');
        }
    }
}
