<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Registrar\Domain\Model\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSite extends AggregateRoot
{
    public const CLASS_SHORTCUT = 'GRAVE_SITE';
    public const CLASS_LABEL    = 'участок на кладбище';

    /**
     * @var PositionInRow|null
     */
    private ?PositionInRow $positionInRow = null;

    /**
     * @var GeoPosition|null
     */
    private ?GeoPosition $geoPosition = null;

    /**
     * @var GraveSiteSize|null
     */
    private ?GraveSiteSize $size = null;

    /**
     * @param GraveSiteId     $id
     * @param CemeteryBlockId $cemeteryBlockId
     * @param RowInBlock      $rowInBlock
     */
    public function __construct(
        private readonly GraveSiteId $id,
        private CemeteryBlockId      $cemeteryBlockId,
        private RowInBlock           $rowInBlock,
    ) {
        parent::__construct();
    }

    /**
     * @return GraveSiteId
     */
    public function id(): GraveSiteId
    {
        return $this->id;
    }

    /**
     * @return CemeteryBlockId
     */
    public function cemeteryBlockId(): CemeteryBlockId
    {
        return $this->cemeteryBlockId;
    }

    /**
     * @param CemeteryBlockId $cemeteryBlockId
     *
     * @return $this
     */
    public function setCemeteryBlockId(CemeteryBlockId $cemeteryBlockId): self
    {
        $this->cemeteryBlockId = $cemeteryBlockId;

        return $this;
    }

    /**
     * @return RowInBlock
     */
    public function rowInBlock(): RowInBlock
    {
        return $this->rowInBlock;
    }

    /**
     * @param RowInBlock $rowInBlock
     *
     * @return $this
     */
    public function setRowInBlock(RowInBlock $rowInBlock): self
    {
        $this->rowInBlock = $rowInBlock;

        return $this;
    }

    /**
     * @return PositionInRow|null
     */
    public function positionInRow(): ?PositionInRow
    {
        return $this->positionInRow;
    }

    /**
     * @param PositionInRow|null $positionInRow
     *
     * @return $this
     */
    public function setPositionInRow(?PositionInRow $positionInRow): self
    {
        $this->positionInRow = $positionInRow;

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

    /**
     * @return GraveSiteSize|null
     */
    public function size(): ?GraveSiteSize
    {
        return $this->size;
    }

    /**
     * @param GraveSiteSize|null $size
     *
     * @return $this
     */
    public function setSize(?GraveSiteSize $size): self
    {
        $this->size = $size;

        return $this;
    }
}
