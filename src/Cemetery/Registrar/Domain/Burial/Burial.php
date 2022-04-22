<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AggregateRoot;
use Cemetery\Registrar\Domain\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use Cemetery\Registrar\Domain\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Burial extends AggregateRoot
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
     * @param BurialType $burialType
     */
    public function __construct(
        private readonly BurialId   $id,
        private readonly BurialCode $code,
        private DeceasedId          $deceasedId,
        private BurialType          $burialType,
    ) {
        parent::__construct();
    }

    /**
     * @return BurialId
     */
    public function id(): BurialId
    {
        return $this->id;
    }

    /**
     * @return BurialCode
     */
    public function code(): BurialCode
    {
        return $this->code;
    }

    /**
     * @return DeceasedId
     */
    public function deceasedId(): DeceasedId
    {
        return $this->deceasedId;
    }

    /**
     * @param DeceasedId $deceasedId
     *
     * @return $this
     */
    public function setDeceasedId(DeceasedId $deceasedId): self
    {
        $this->deceasedId = $deceasedId;

        return $this;
    }

    /**
     * @return BurialType
     */
    public function burialType(): BurialType
    {
        return $this->burialType;
    }

    /**
     * @param BurialType $burialType
     *
     * @return $this
     */
    public function setBurialType(BurialType $burialType): self
    {
        $this->burialType = $burialType;

        return $this;
    }

    /**
     * @return CustomerId|null
     */
    public function customerId(): ?CustomerId
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
    public function burialPlaceId(): ?BurialPlaceId
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
        $this->assertMatchesBurialType($burialPlaceId);
        $this->burialPlaceId = $burialPlaceId;

        return $this;
    }

    /**
     * @return NaturalPersonId|null
     */
    public function burialPlaceOwnerId(): ?NaturalPersonId
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
    public function funeralCompanyId(): ?FuneralCompanyId
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
    public function buriedAt(): ?\DateTimeImmutable
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

    /**
     * Checks that the burial place matches the burial type.
     *
     * @param BurialPlaceId|null $burialPlaceId
     *
     * @throws \RuntimeException when the burial place does not match the burial type
     */
    private function assertMatchesBurialType(?BurialPlaceId $burialPlaceId): void
    {
        if ($burialPlaceId === null) {
            return;
        }

        $id      = $burialPlaceId->id();
        $matches = match (true) {
            $this->burialType()->isCoffinInGraveSite(),
            $this->burialType()->isUrnInGraveSite()         => $id instanceof GraveSiteId,
            $this->burialType()->isUrnInColumbariumNiche()  => $id instanceof ColumbariumNicheId,
            $this->burialType()->isAshesUnderMemorialTree() => $id instanceof MemorialTreeId,
            default => false,
        };
        if (!$matches) {
            throw new \RuntimeException(\sprintf(
                'Место захоронения "%s" не соответствует типу захороненния "%s".',
                $this->getBurialPlaceLabel($burialPlaceId),
                $this->burialType()->label(),
            ));
        }
    }

    /**
     * Returns burial place label for the burial place ID.
     *
     * @param BurialPlaceId $burialPlaceId
     *
     * @return string
     */
    private function getBurialPlaceLabel(BurialPlaceId $burialPlaceId): string
    {
        $id = $burialPlaceId->id();

        return match (true) {
            $id instanceof GraveSiteId        => 'могила',
            $id instanceof ColumbariumNicheId => 'колумбарная ниша',
            $id instanceof MemorialTreeId     => 'памятное дерево',
        };
    }
}
