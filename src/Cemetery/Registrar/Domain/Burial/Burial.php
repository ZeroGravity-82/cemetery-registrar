<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
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
     * @var BurialContainerId|null
     */
    private ?BurialContainerId $burialContainerId = null;

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
     */
    public function setCustomerId(?CustomerId $customerId): void
    {
        $this->customerId = $customerId;
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
     */
    public function setBurialPlaceId(?BurialPlaceId $burialPlaceId): void
    {
        $this->burialPlaceId = $burialPlaceId;
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
     */
    public function setBurialPlaceOwnerId(?NaturalPersonId $burialPlaceOwnerId): void
    {
        $this->burialPlaceOwnerId = $burialPlaceOwnerId;
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
     */
    public function setFuneralCompanyId(?FuneralCompanyId $funeralCompanyId): void
    {
        $this->funeralCompanyId = $funeralCompanyId;
    }

    /**
     * @return BurialContainerId|null
     */
    public function getBurialContainerId(): ?BurialContainerId
    {
        return $this->burialContainerId;
    }

    /**
     * @param BurialContainerId|null $burialContainerId
     */
    public function setBurialContainerId(?BurialContainerId $burialContainerId): void
    {
        $this->burialContainerId = $burialContainerId;
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
     */
    public function setBuriedAt(?\DateTimeImmutable $buriedAt): void
    {
        $this->buriedAt = $buriedAt;
    }
}
