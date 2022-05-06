<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class BurialBuilder
{
    /**
     * @var Burial
     */
    private Burial $burial;

    /**
     * @param IdentityGenerator   $identityGenerator
     * @param BurialCodeGenerator $burialCodeGenerator
     */
    public function __construct(
        private readonly IdentityGenerator   $identityGenerator,
        private readonly BurialCodeGenerator $burialCodeGenerator,
    ) {}

    /**
     * @param DeceasedId $deceasedId
     * @param BurialType $burialType
     *
     * @return $this
     */
    public function initialize(DeceasedId $deceasedId, BurialType $burialType): self
    {
        $this->burial = new Burial(
            new BurialId($this->identityGenerator->getNextIdentity()),
            new BurialCode($this->burialCodeGenerator->getNextCode()),
            $deceasedId,
            $burialType,
        );

        return $this;
    }

    /**
     * @param CustomerId $customerId
     *
     * @return $this
     */
    public function addCustomerId(CustomerId $customerId): self
    {
        $this->assertInitialized();
        $this->burial->setCustomerId($customerId);

        return $this;
    }

    /**
     * @param BurialPlaceId $burialPlaceId
     *
     * @return $this
     */
    public function addBurialPlaceId(BurialPlaceId $burialPlaceId): self
    {
        $this->assertInitialized();
        $this->burial->setBurialPlaceId($burialPlaceId);

        return $this;
    }

    /**
     * @param NaturalPersonId $burialPlaceOwnerId
     *
     * @return $this
     */
    public function addBurialPlaceOwnerId(NaturalPersonId $burialPlaceOwnerId): self
    {
        $this->assertInitialized();
        $this->burial->setBurialPlaceOwnerId($burialPlaceOwnerId);

        return $this;
    }

    /**
     * @param FuneralCompanyId $funeralCompanyId
     *
     * @return $this
     */
    public function addFuneralCompanyId(FuneralCompanyId $funeralCompanyId): self
    {
        $this->assertInitialized();
        $this->burial->setFuneralCompanyId($funeralCompanyId);

        return $this;
    }

    /**
     * @param BurialContainer $burialContainer
     *
     * @return $this
     */
    public function addBurialContainer(BurialContainer $burialContainer): self
    {
        $this->assertInitialized();
        $this->burial->setBurialContainer($burialContainer);

        return $this;
    }

    /**
     * @param \DateTimeImmutable $buriedAt
     *
     * @return $this
     */
    public function addBuriedAt(\DateTimeImmutable $buriedAt): self
    {
        $this->assertInitialized();
        $this->burial->setBuriedAt($buriedAt);

        return $this;
    }

    /**
     * @return Burial
     */
    public function build(): Burial
    {
        $this->assertInitialized();
        $burial = $this->burial;
        unset($this->burial);

        return $burial;
    }

    /**
     * @throws \LogicException when the burial is not initialized
     */
    private function assertInitialized(): void
    {
        if (!isset($this->burial)) {
            throw new \LogicException(\sprintf('Строитель для класса %s не инициализирован.', Burial::class));
        }
    }
}
