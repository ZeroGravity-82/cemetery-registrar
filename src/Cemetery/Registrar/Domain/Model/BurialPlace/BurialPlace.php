<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class BurialPlace extends AggregateRoot
{
    protected ?NaturalPersonId $personInChargeId = null;
    protected ?GeoPosition     $geoPosition = null;

    public function personInChargeId(): ?NaturalPersonId
    {
        return $this->personInChargeId;
    }

    public function setPersonInCharge(?NaturalPerson $personInCharge): self
    {
        $this->personInChargeId = $personInCharge?->id();

        return $this;
    }

    public function geoPosition(): ?GeoPosition
    {
        return $this->geoPosition;
    }

    public function setGeoPosition(?GeoPosition $geoPosition): self
    {
        $this->geoPosition = $geoPosition;

        return $this;
    }
}
