<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\AggregateRoot;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNiche extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'COLUMBARIUM_NICHE';
    public const CLASS_LABEL    = 'колумбарная ниша';

    /**
     * @var GeoPosition|null
     */
    private ?GeoPosition $geoPosition = null;

    /**
     * @param ColumbariumNicheId     $id
     * @param ColumbariumId          $columbariumId
     * @param RowInColumbarium       $rowInColumbarium
     * @param ColumbariumNicheNumber $nicheNumber
     */
    public function __construct(
        private readonly ColumbariumNicheId $id,
        private ColumbariumId               $columbariumId,
        private RowInColumbarium            $rowInColumbarium,
        private ColumbariumNicheNumber      $nicheNumber,
    ) {
        parent::__construct();
    }

    /**
     * @return ColumbariumNicheId
     */
    public function id(): ColumbariumNicheId
    {
        return $this->id;
    }

    /**
     * @return ColumbariumId
     */
    public function columbariumId(): ColumbariumId
    {
        return $this->columbariumId;
    }

    /**
     * @param ColumbariumId $columbariumId
     *
     * @return $this
     */
    public function setColumbariumId(ColumbariumId $columbariumId): self
    {
        $this->columbariumId = $columbariumId;

        return $this;
    }

    /**
     * @return RowInColumbarium
     */
    public function rowInColumbarium(): RowInColumbarium
    {
        return $this->rowInColumbarium;
    }

    /**
     * @param RowInColumbarium $rowInColumbarium
     *
     * @return $this
     */
    public function setRowInColumbarium(RowInColumbarium $rowInColumbarium): self
    {
        $this->rowInColumbarium = $rowInColumbarium;

        return $this;
    }

    /**
     * @return ColumbariumNicheNumber
     */
    public function nicheNumber(): ColumbariumNicheNumber
    {
        return $this->nicheNumber;
    }

    /**
     * @param ColumbariumNicheNumber $nicheNumber
     *
     * @return $this
     */
    public function setNicheNumber(ColumbariumNicheNumber $nicheNumber): self
    {
        $this->nicheNumber = $nicheNumber;

        return $this;
    }

    /**
     * @return GeoPosition|null
     */
    public function geoPosition(): ?GeoPosition
    {
        return $this->geoPosition;
    }

    /**
     * @param GeoPosition|null $geoPosition
     *
     * @return $this
     */
    public function setGeoPosition(?GeoPosition $geoPosition): self
    {
        $this->geoPosition = $geoPosition;

        return $this;
    }
}
