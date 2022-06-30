<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Deceased;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Deceased extends AggregateRoot
{
    /**
     * @var Age|null
     */
    private ?Age $age = null;

    /**
     * @var DeathCertificateId|null
     */
    private ?DeathCertificateId $deathCertificateId = null;

    /**
     * @var CauseOfDeathId|null
     */
    private ?CauseOfDeathId $causeOfDeathId = null;

    /**
     * @var CremationCertificateId|null
     */
    private ?CremationCertificateId $cremationCertificateId = null;

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
     * @return Age|null
     */
    public function age(): ?Age
    {
        return $this->age;
    }

    /**
     * @param Age|null $age
     *
     * @return $this
     */
    public function setAge(?Age $age): self
    {
        $this->age = $age;

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
     * @return CauseOfDeathId|null
     */
    public function causeOfDeathId(): ?CauseOfDeathId
    {
        return $this->causeOfDeathId;
    }

    /**
     * @param CauseOfDeathId|null $causeOfDeathId
     *
     * @return $this
     */
    public function setCauseOfDeathId(?CauseOfDeathId $causeOfDeathId): self
    {
        $this->causeOfDeathId = $causeOfDeathId;

        return $this;
    }

    /**
     * @return CremationCertificateId|null
     */
    public function cremationCertificateId(): ?CremationCertificateId
    {
        return $this->cremationCertificateId;
    }

    /**
     * @param CremationCertificateId|null $cremationCertificateId
     *
     * @return $this
     */
    public function setCremationCertificateId(?CremationCertificateId $cremationCertificateId): self
    {
        $this->cremationCertificateId = $cremationCertificateId;

        return $this;
    }
}
