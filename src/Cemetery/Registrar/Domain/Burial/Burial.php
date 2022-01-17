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
     * @param BurialId             $id
     * @param NaturalPersonId      $deceasedId
     * @param CustomerId           $customerId
     * @param SiteId               $siteId
     * @param NaturalPersonId|null $siteOwnerId
     */
    public function __construct(
        private BurialId         $id,
        private NaturalPersonId  $deceasedId,
        private CustomerId       $customerId,
        private SiteId           $siteId,
        private ?NaturalPersonId $siteOwnerId,
    ) {}

    /**
     * @return BurialId
     */
    public function getId(): BurialId
    {
        return $this->id;
    }

    /**
     * @return NaturalPersonId
     */
    public function getDeceasedId(): NaturalPersonId
    {
        return $this->deceasedId;
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }

    /**
     * @return SiteId
     */
    public function getSiteId(): SiteId
    {
        return $this->siteId;
    }

    /**
     * @return NaturalPersonId|null
     */
    public function getSiteOwnerId(): ?NaturalPersonId
    {
        return $this->siteOwnerId;
    }





}
