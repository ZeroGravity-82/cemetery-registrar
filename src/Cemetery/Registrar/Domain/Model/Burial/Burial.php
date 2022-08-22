<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\BurialContainer;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Coffin;
use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\Urn;
use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlace;
use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\FuneralCompany\FuneralCompanyId;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class Burial extends AggregateRoot
{
    private NaturalPersonId|JuristicPersonId|SoleProprietorId|null $customerId = null;
    private ?BurialPlaceId                                         $burialPlaceId = null;
    private ?FuneralCompanyId                                      $funeralCompanyId = null;
    private ?BurialContainer                                       $burialContainer = null;
    private ?\DateTimeImmutable                                    $buriedAt = null;
    private ?BurialChainId                                         $burialChainId = null;

    public function __construct(
        private BurialId        $id,
        private BurialCode      $code,
        private BurialType      $type,
        private NaturalPersonId $deceasedId,
    ) {
        parent::__construct();
    }

    public function id(): BurialId
    {
        return $this->id;
    }

    public function code(): BurialCode
    {
        return $this->code;
    }

    public function type(): BurialType
    {
        return $this->type;
    }

    /**
     * @throws Exception when the burial place does not match the burial type
     * @throws Exception when the funeral company not allowed for the burial type
     * @throws Exception when the burial container does not match the burial type
     */
    public function setType(BurialType $type): self
    {
        // $this->assertBurialTypeMatchesDeceased($type);
        if ($this->type !== $type) {
            $this->discardBurialPlace();
            $this->setFuneralCompanyId(null);
            $this->setBurialContainer(null);
        }
        $this->type = $type;

        return $this;
    }

    public function deceasedId(): NaturalPersonId
    {
        return $this->deceasedId;
    }

    public function setDeceasedId(NaturalPersonId $deceasedId): self
    {
        // $this->assertDeceasedMatchesBurialType($deceasedId);
        $this->deceasedId = $deceasedId;

        return $this;
    }

    public function customerId(): NaturalPersonId|JuristicPersonId|SoleProprietorId|null
    {
        return $this->customerId;
    }

    public function assignCustomer(NaturalPerson|JuristicPerson|SoleProprietor $customer): self
    {
        $this->customerId = $customer->id();

        return $this;
    }

    public function discardCustomer(): self
    {
        $this->customerId = null;

        return $this;
    }

    public function burialPlaceId(): ?BurialPlaceId
    {
        return $this->burialPlaceId;
    }

    /**
     * @throws Exception when the burial place does not match the burial type
     */
    public function assignBurialPlace(BurialPlace $burialPlace): self
    {
        $this->assertBurialPlaceMatchesBurialType($burialPlace);
        /** @var BurialPlaceId $burialPlaceId */
        $burialPlaceId       = $burialPlace->id();
        $this->burialPlaceId = $burialPlaceId;

        return $this;
    }

    public function discardBurialPlace(): self
    {
        $this->burialPlaceId = null;

        return $this;
    }

    public function funeralCompanyId(): ?FuneralCompanyId
    {
        return $this->funeralCompanyId;
    }

    /**
     * @throws Exception when the funeral company not allowed for the burial type
     */
    public function setFuneralCompanyId(?FuneralCompanyId $funeralCompanyId): self
    {
        $this->assertFuneralCompanyAllowedForBurialType($funeralCompanyId);
        $this->funeralCompanyId = $funeralCompanyId;

        return $this;
    }

    public function burialContainer(): ?BurialContainer
    {
        return $this->burialContainer;
    }

    /**
     * @throws Exception when the burial container does not match the burial type
     */
    public function setBurialContainer(?BurialContainer $burialContainer): self
    {
        $this->assertBurialContainerMatchesBurialType($burialContainer);
        $this->burialContainer = $burialContainer;

        return $this;
    }

    public function buriedAt(): ?\DateTimeImmutable
    {
        return $this->buriedAt;
    }

    public function setBuriedAt(?\DateTimeImmutable $buriedAt): self
    {
        $this->buriedAt = $buriedAt;

        return $this;
    }

    public function burialChainId(): ?BurialChainId
    {
        return $this->burialChainId;
    }

    public function setBurialChainId(?BurialChainId $burialChainId): self
    {
        $this->burialChainId = $burialChainId;

        return $this;
    }

    /**
     * @throws Exception when the burial place does not match the burial type
     */
    private function assertBurialPlaceMatchesBurialType(BurialPlace $burialPlace): void
    {
        $id      = $burialPlace->id();
        $isMatch = match (true) {
            $this->type()->isCoffinInGraveSite(),
            $this->type()->isUrnInGraveSite()         => $id instanceof GraveSiteId,
            $this->type()->isUrnInColumbariumNiche()  => $id instanceof ColumbariumNicheId,
            $this->type()->isAshesUnderMemorialTree() => $id instanceof MemorialTreeId,
            default => false,
        };
        if (!$isMatch) {
            throw new Exception(\sprintf(
                'Место захоронения "%s" не соответствует типу захоронения "%s".',
                $burialPlace::CLASS_LABEL,
                $this->type()->label(),
            ));
        }
    }

    /**
     * @throws Exception when the burial container does not match the burial type
     */
    private function assertBurialContainerMatchesBurialType(?BurialContainer $burialContainer): void
    {
        $container = $burialContainer?->container();
        $isMatch   = match (true) {
            $this->type()->isCoffinInGraveSite()      => $container === null || $container instanceof Coffin,
            $this->type()->isUrnInGraveSite(),
            $this->type()->isUrnInColumbariumNiche()  => $container === null || $container instanceof Urn,
            $this->type()->isAshesUnderMemorialTree() => $container === null,
            default => false,
        };
        if (!$isMatch) {
            throw new Exception(\sprintf(
                'Контейнер захоронения "%s" не соответствует типу захоронения "%s".',
                $container::CLASS_LABEL,
                $this->type()->label(),
            ));
        }
    }

    /**
     * @throws Exception when the funeral company not allowed for the burial type
     */
    private function assertFuneralCompanyAllowedForBurialType(?FuneralCompanyId $funeralCompanyId): void
    {
        $isAllowed = match (true) {
            $this->type()->isCoffinInGraveSite()      => true,
            $this->type()->isUrnInGraveSite(),
            $this->type()->isUrnInColumbariumNiche(),
            $this->type()->isAshesUnderMemorialTree() => $funeralCompanyId === null,
            default => false,
        };
        if (!$isAllowed) {
            throw new Exception(\sprintf(
                'Похоронная фирма не может быть указана для типа захоронения "%s".',
                $this->type()->label(),
            ));
        }
    }
}
