<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\IdentityGeneratorInterface;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class DeceasedBuilder
{
    /**
     * @var Deceased
     */
    private Deceased $deceased;

    /**
     * @param IdentityGeneratorInterface $identityGenerator
     */
    public function __construct(
        private IdentityGeneratorInterface $identityGenerator,
    ) {}

    /**
     * @param string             $naturalPersonId
     * @param \DateTimeImmutable $diedAt
     *
     * @return $this
     */
    public function initialize(string $naturalPersonId, \DateTimeImmutable $diedAt): self
    {
        $naturalPersonId = new NaturalPersonId($naturalPersonId);
        $this->deceased  = new Deceased(
            new DeceasedId($this->identityGenerator->getNextIdentity()),
            $naturalPersonId,
            $diedAt,
        );

        return $this;
    }

    /**
     * @param string|null $deathCertificateId
     *
     * @return $this
     */
    public function addDeathCertificateId(?string $deathCertificateId): self
    {
        $this->assertInitialized();
        $deathCertificateId = $deathCertificateId ? new DeathCertificateId($deathCertificateId) : null;
        $this->deceased->setDeathCertificateId($deathCertificateId);

        return $this;
    }

    /**
     * @param string|null $causeOfDeath
     *
     * @return $this
     */
    public function addCauseOfDeath(?string $causeOfDeath): self
    {
        $this->assertInitialized();
        $causeOfDeath = $causeOfDeath ? new CauseOfDeath($causeOfDeath): null;
        $this->deceased->setCauseOfDeath($causeOfDeath);

        return $this;
    }

    /**
     * @return Deceased
     */
    public function build(): Deceased
    {
        $this->assertInitialized();
        $deceased = $this->deceased;
        unset($this->deceased);

        return $deceased;
    }

    /**
     * @throws \LogicException when the deceased builder is not initialized
     */
    private function assertInitialized(): void
    {
        if (!isset($this->deceased)) {
            throw new \LogicException('The deceased builder is not initialized.');
        }
    }
}
