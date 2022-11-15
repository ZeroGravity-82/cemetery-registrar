<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\AbstractBurialPlace;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSite extends AbstractBurialPlace
{
    public const CLASS_SHORTCUT = 'GRAVE_SITE';
    public const CLASS_LABEL    = 'участок';

    private ?PositionInRow $positionInRow = null;
    private ?GraveSiteSize $size = null;

    public function __construct(
        private GraveSiteId     $id,
        private CemeteryBlockId $cemeteryBlockId,
        private RowInBlock      $rowInBlock,
    ) {
        parent::__construct();
    }

    public function id(): GraveSiteId
    {
        return $this->id;
    }

    public function cemeteryBlockId(): CemeteryBlockId
    {
        return $this->cemeteryBlockId;
    }

    public function assignCemeteryBlock(CemeteryBlock $cemeteryBlock): self
    {
        $this->cemeteryBlockId = $cemeteryBlock->id();

        return $this;
    }

    public function rowInBlock(): RowInBlock
    {
        return $this->rowInBlock;
    }

    public function setRowInBlock(RowInBlock $rowInBlock): self
    {
        $this->rowInBlock = $rowInBlock;

        return $this;
    }

    public function positionInRow(): ?PositionInRow
    {
        return $this->positionInRow;
    }

    public function setPositionInRow(?PositionInRow $positionInRow): self
    {
        $this->positionInRow = $positionInRow;

        return $this;
    }

    public function size(): ?GraveSiteSize
    {
        return $this->size;
    }

    public function setSize(?GraveSiteSize $size): self
    {
        $this->size = $size;

        return $this;
    }
}
