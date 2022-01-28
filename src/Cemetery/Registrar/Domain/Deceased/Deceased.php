<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Deceased extends AbstractAggregateRoot
{
    /**
     * @param DeceasedId              $id
     * @param NaturalPersonId         $naturalPersonId
     * @param \DateTimeImmutable      $diedAt
     * @param DeathCertificateId|null $deathCertificateId
     * @param CauseOfDeath|null       $causeOfDeath
     */
    public function __construct(
        private DeceasedId          $id,
        private NaturalPersonId     $naturalPersonId,
        private \DateTimeImmutable  $diedAt,
        private ?DeathCertificateId $deathCertificateId,
        private ?CauseOfDeath       $causeOfDeath,
    ) {
        parent::__construct();
    }

    /**
     * @return DeceasedId
     */
    public function getId(): DeceasedId
    {
        return $this->id;
    }

    /**
     * @return NaturalPersonId
     */
    public function getNaturalPersonId(): NaturalPersonId
    {
        return $this->naturalPersonId;
    }

    /**
     * @param NaturalPersonId $naturalPersonId
     */
    public function setNaturalPersonId(NaturalPersonId $naturalPersonId): void
    {
        $this->naturalPersonId = $naturalPersonId;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDiedAt(): \DateTimeImmutable
    {
        return $this->diedAt;
    }

    /**
     * @param \DateTimeImmutable $diedAt
     */
    public function setDiedAt(\DateTimeImmutable $diedAt): void
    {
        $this->diedAt = $diedAt;
    }

    /**
     * @return DeathCertificateId|null
     */
    public function getDeathCertificateId(): ?DeathCertificateId
    {
        return $this->deathCertificateId;
    }

    /**
     * @param DeathCertificateId|null $deathCertificateId
     */
    public function setDeathCertificateId(?DeathCertificateId $deathCertificateId): void
    {
        $this->deathCertificateId = $deathCertificateId;
    }

    /**
     * @return CauseOfDeath|null
     */
    public function getCauseOfDeath(): ?CauseOfDeath
    {
        return $this->causeOfDeath;
    }

    /**
     * @param CauseOfDeath|null $causeOfDeath
     */
    public function setCauseOfDeath(?CauseOfDeath $causeOfDeath): void
    {
        $this->causeOfDeath = $causeOfDeath;
    }
}
