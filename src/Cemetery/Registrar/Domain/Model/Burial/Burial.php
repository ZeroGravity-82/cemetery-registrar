<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Deceased\DeceasedId;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Burial extends AggregateRoot
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
    private ?NaturalPersonId $personInChargeId = null;

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
     * @var BurialChainId|null
     */
    private ?BurialChainId $burialChainId = null;

    /**
     * @param BurialId   $id
     * @param BurialCode $code
     * @param BurialType $type
     * @param DeceasedId $deceasedId
     */
    public function __construct(
        private readonly BurialId   $id,
        private readonly BurialCode $code,
        private BurialType          $type,
        private DeceasedId          $deceasedId,
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
    public function type(): BurialType
    {
        return $this->type;
    }

    /**
     * @param BurialType $type
     *
     * @return $this
     */
    public function setType(BurialType $type): self
    {
        if ($this->type !== $type) {
            $this->setBurialPlaceId(null);
            $this->setBurialContainer(null);
        }
        $this->type = $type;

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
        $this->assertBurialPlaceMatchesBurialType($burialPlaceId);
        $this->burialPlaceId = $burialPlaceId;

        return $this;
    }

    /**
     * @return NaturalPersonId|null
     */
    public function personInChargeId(): ?NaturalPersonId
    {
        return $this->personInChargeId;
    }

    /**
     * @param NaturalPersonId|null $personInChargeId
     *
     * @return $this
     */
    public function setPersonInChargeId(?NaturalPersonId $personInChargeId): self
    {
        $this->personInChargeId = $personInChargeId;

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
        $this->assertBurialContainerMatchesBurialType($burialContainer);
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
     * @return BurialChainId|null
     */
    public function burialChainId(): ?BurialChainId
    {
        return $this->burialChainId;
    }

    /**
     * @param BurialChainId|null $burialChainId
     *
     * @return $this
     */
    public function setBurialChainId(?BurialChainId $burialChainId): self
    {
        $this->burialChainId = $burialChainId;

        return $this;
    }

    /**
     * Checks that the burial place matches the burial type.
     *
     * @param BurialPlaceId|null $burialPlaceId
     *
     * @throws \RuntimeException when the burial place does not match the burial type
     */
    private function assertBurialPlaceMatchesBurialType(?BurialPlaceId $burialPlaceId): void
    {
        $id      = $burialPlaceId?->id();
        $matches = match (true) {
            $this->type()->isCoffinInGraveSite(),
            $this->type()->isUrnInGraveSite()         => $id === null || $id instanceof GraveSiteId,
            $this->type()->isUrnInColumbariumNiche()  => $id === null || $id instanceof ColumbariumNicheId,
            $this->type()->isAshesUnderMemorialTree() => $id === null || $id instanceof MemorialTreeId,
            default => false,
        };
        if (!$matches) {
            throw new \RuntimeException(\sprintf(
                'Место захоронения "%s" не соответствует типу захороненния "%s".',
                $this->getBurialPlaceLabel($burialPlaceId),
                $this->type()->label(),
            ));
        }
    }

    /**
     * Checks that the burial container matches the burial type.
     *
     * @param BurialContainer|null $burialContainer
     *
     * @throws \RuntimeException when the burial container does not match the burial type
     */
    private function assertBurialContainerMatchesBurialType(?BurialContainer $burialContainer): void
    {
        $container = $burialContainer?->container();
        $matches   = match (true) {
            $this->type()->isCoffinInGraveSite()      => $container === null || $container instanceof Coffin,
            $this->type()->isUrnInGraveSite(),
            $this->type()->isUrnInColumbariumNiche()  => $container === null || $container instanceof Urn,
            $this->type()->isAshesUnderMemorialTree() => $container === null,
            default => false,
        };
        if (!$matches) {
            throw new \RuntimeException(\sprintf(
                'Контейнер захоронения "%s" не соответствует типу захороненния "%s".',
                $container::CLASS_LABEL,
                $this->type()->label(),
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
