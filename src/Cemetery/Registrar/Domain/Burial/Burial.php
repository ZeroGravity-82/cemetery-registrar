<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\AbstractAggregateRoot;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Site\SiteId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class Burial extends AbstractAggregateRoot
{
    /**
     * @param BurialId              $id
     * @param BurialCode            $code
     * @param NaturalPersonId       $deceasedId
     * @param SiteId                $siteId
     * @param CustomerId|null       $customerId
     * @param NaturalPersonId|null  $siteOwnerId
     * @param FuneralCompanyId|null $funeralCompanyId
     */
    public function __construct(
        private BurialId          $id,
        private BurialCode        $code,
        private NaturalPersonId   $deceasedId,
        private SiteId            $siteId,
        private ?CustomerId       $customerId,
        private ?NaturalPersonId  $siteOwnerId,
        private ?FuneralCompanyId $funeralCompanyId,
    ) {}

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
     * @return NaturalPersonId
     */
    public function getDeceasedId(): NaturalPersonId
    {
        return $this->deceasedId;
    }

    /**
     * @return SiteId
     */
    public function getSiteId(): SiteId
    {
        return $this->siteId;
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
     * @return NaturalPersonId|null
     */
    public function getSiteOwnerId(): ?NaturalPersonId
    {
        return $this->siteOwnerId;
    }

    /**
     * @param NaturalPersonId|null $siteOwnerId
     */
    public function setSiteOwnerId(?NaturalPersonId $siteOwnerId): void
    {
        $this->siteOwnerId = $siteOwnerId;
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
}
