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
    public const CLASS_LABEL = null;

    protected ?NaturalPersonId $personInChargeId = null;
    protected ?GeoPosition     $geoPosition      = null;

    public function personInChargeId(): ?NaturalPersonId
    {
        return $this->personInChargeId;
    }

    public function assignPersonInCharge(NaturalPerson $personInCharge): self
    {
        if ($personInCharge->deceasedDetails() !== null) {
            throw new \LogicException(\sprintf(
                'Невозможно назначить умершего с ID "%s" в качестве ответственного для места захоронения с ID "%s" и типом "%s".',
                $personInCharge->id()->value(),
                $this->id()->value(),
                self::CLASS_LABEL,
            ));
        }
        $this->personInChargeId = $personInCharge->id();

        return $this;
    }

    public function discardPersonInCharge(): self
    {
        if ($this->personInChargeId() === null) {
            throw new \LogicException(\sprintf(
                'Невозможно удалить ответственного для места захоронения с ID "%s" и типом "%s", т.к. никто не назначен ответственным.',
                $this->id()->value(),
                self::CLASS_LABEL,
            ));
        }
        $this->personInChargeId = null;

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
