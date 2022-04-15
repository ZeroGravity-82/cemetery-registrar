<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Burial extends AbstractAggregateRoot
{
    /**
     * @var CustomerId|null
     */
    private ?CustomerId $customerId = null;

    /**
     * @var BurialPlaceId|null
     */
    private ?BurialPlaceId $burialPlaceId = null;

    /**
     * @var NaturalPersonId|null
     */
    private ?NaturalPersonId $burialPlaceOwnerId = null;

    /**
     * @var FuneralCompanyId|null
     */
    private ?FuneralCompanyId $funeralCompanyId = null;

    /**
     * @var BurialContainer|null
     */
    private ?BurialContainer $burialContainer = null;

    /**
     * @var \DateTimeImmutable|null
     */
    private ?\DateTimeImmutable $buriedAt = null;

    /**
     * @param BurialId   $id
     * @param BurialCode $code
     * @param DeceasedId $deceasedId
     */
    public function __construct(
        private BurialId   $id,
        private BurialCode $code,
        private DeceasedId $deceasedId,
    ) {
        parent::__construct();
    }

    /**
     * @return BurialId
     */
    public function getId(): BurialId
    {
        return $this->id;
    }

    /**
     * @return BurialCode
     */
    public function getCode(): BurialCode
    {
        return $this->code;
    }

    /**
     * @return DeceasedId
     */
    public function getDeceasedId(): DeceasedId
    {
        return $this->deceasedId;
    }

    /**
     * @return CustomerId|null
     */
    public function getCustomerId(): ?CustomerId
    {
        return $this->customerId;
    }

    /**
     * @param CustomerId|null $customerId
     *
     * @return $this
     */
    public function setCustomerId(?CustomerId $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    /**
     * @return BurialPlaceId|null
     */
    public function getBurialPlaceId(): ?BurialPlaceId
    {
        return $this->burialPlaceId;
    }

    /**
     * @param BurialPlaceId|null $burialPlaceId
     *
     * @return $this
     */
    public function setBurialPlaceId(?BurialPlaceId $burialPlaceId): self
    {
        $this->burialPlaceId = $burialPlaceId;

        return $this;
    }

    /**
     * @return NaturalPersonId|null
     */
    public function getBurialPlaceOwnerId(): ?NaturalPersonId
    {
        return $this->burialPlaceOwnerId;
    }

    /**
     * @param NaturalPersonId|null $burialPlaceOwnerId
     *
     * @return $this
     */
    public function setBurialPlaceOwnerId(?NaturalPersonId $burialPlaceOwnerId): self
    {
        $this->burialPlaceOwnerId = $burialPlaceOwnerId;

        return $this;
    }

    /**
     * @return FuneralCompanyId|null
     */
    public function getFuneralCompanyId(): ?FuneralCompanyId
    {
        return $this->funeralCompanyId;
    }

    /**
     * @param FuneralCompanyId|null $funeralCompanyId
     *
     * @return $this
     */
    public function setFuneralCompanyId(?FuneralCompanyId $funeralCompanyId): self
    {
        $this->funeralCompanyId = $funeralCompanyId;

        return $this;
    }

    /**
     * @return BurialContainer|null
     */
    public function burialContainer(): ?BurialContainer
    {
        return $this->burialContainer;
    }

    /**
     * @param BurialContainer|null $burialContainer
     *
     * @return $this
     */
    public function setBurialContainer(?BurialContainer $burialContainer): self
    {
        $this->burialContainer = $burialContainer;

        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getBuriedAt(): ?\DateTimeImmutable
    {
        return $this->buriedAt;
    }

    /**
     * @param \DateTimeImmutable|null $buriedAt
     *
     * @return $this
     */
    public function setBuriedAt(?\DateTimeImmutable $buriedAt): self
    {
        $this->buriedAt = $buriedAt;

        return $this;
    }
}
