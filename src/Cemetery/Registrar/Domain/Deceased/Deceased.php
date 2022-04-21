<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\AggregateRoot;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Deceased extends AggregateRoot
{
    /**
     * @var DeathCertificateId|null
     */
    private ?DeathCertificateId $deathCertificateId = null;

    /**
     * @var CauseOfDeath|null
     */
    private ?CauseOfDeath $causeOfDeath = null;

    /**
     * @param DeceasedId         $id
     * @param NaturalPersonId    $naturalPersonId
     * @param \DateTimeImmutable $diedAt
     */
    public function __construct(
        private readonly DeceasedId $id,
        private NaturalPersonId     $naturalPersonId,
        private \DateTimeImmutable  $diedAt,

    ) {
        parent::__construct();
    }

    /**
     * @return DeceasedId
     */
    public function id(): DeceasedId
    {
        return $this->id;
    }

    /**
     * @return NaturalPersonId
     */
    public function naturalPersonId(): NaturalPersonId
    {
        return $this->naturalPersonId;
    }

    /**
     * @param NaturalPersonId $naturalPersonId
     *
     * @return $this
     */
    public function setNaturalPersonId(NaturalPersonId $naturalPersonId): self
    {
        $this->naturalPersonId = $naturalPersonId;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function diedAt(): \DateTimeImmutable
    {
        return $this->diedAt;
    }

    /**
     * @param \DateTimeImmutable $diedAt
     *
     * @return $this
     */
    public function setDiedAt(\DateTimeImmutable $diedAt): self
    {
        $this->diedAt = $diedAt;

        return $this;
    }

    /**
     * @return DeathCertificateId|null
     */
    public function deathCertificateId(): ?DeathCertificateId
    {
        return $this->deathCertificateId;
    }

    /**
     * @param DeathCertificateId|null $deathCertificateId
     *
     * @return $this
     */
    public function setDeathCertificateId(?DeathCertificateId $deathCertificateId): self
    {
        $this->deathCertificateId = $deathCertificateId;

        return $this;
    }

    /**
     * @return CauseOfDeath|null
     */
    public function causeOfDeath(): ?CauseOfDeath
    {
        return $this->causeOfDeath;
    }

    /**
     * @param CauseOfDeath|null $causeOfDeath
     *
     * @return $this
     */
    public function setCauseOfDeath(?CauseOfDeath $causeOfDeath): self
    {
        $this->causeOfDeath = $causeOfDeath;

        return $this;
    }
}
