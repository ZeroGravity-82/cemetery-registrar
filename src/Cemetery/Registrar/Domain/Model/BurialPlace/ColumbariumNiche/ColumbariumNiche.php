<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNiche extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'COLUMBARIUM_NICHE';
    public const CLASS_LABEL    = 'колумбарная ниша';

    private ?GeoPosition $geoPosition = null;

    public function __construct(
        private ColumbariumNicheId     $id,
        private ColumbariumId          $columbariumId,
        private RowInColumbarium       $rowInColumbarium,
        private ColumbariumNicheNumber $nicheNumber,
        // TODO add person in charge
    ) {
        parent::__construct();
    }

    public function id(): ColumbariumNicheId
    {
        return $this->id;
    }

    public function columbariumId(): ColumbariumId
    {
        return $this->columbariumId;
    }

    public function setColumbariumId(ColumbariumId $columbariumId): self
    {
        $this->columbariumId = $columbariumId;

        return $this;
    }

    public function rowInColumbarium(): RowInColumbarium
    {
        return $this->rowInColumbarium;
    }

    public function setRowInColumbarium(RowInColumbarium $rowInColumbarium): self
    {
        $this->rowInColumbarium = $rowInColumbarium;

        return $this;
    }

    public function nicheNumber(): ColumbariumNicheNumber
    {
        return $this->nicheNumber;
    }

    public function setNicheNumber(ColumbariumNicheNumber $nicheNumber): self
    {
        $this->nicheNumber = $nicheNumber;

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
