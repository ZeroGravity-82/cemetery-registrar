<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteRemoved extends Event
{
    public function __construct(
        private GraveSiteId     $graveSiteId,
        private CemeteryBlockId $cemeteryBlockId,
        private RowInBlock      $rowInBlock,
        private ?PositionInRow  $positionInRow,
    ) {
        parent::__construct();
    }

    public function graveSiteId(): GraveSiteId
    {
        return $this->graveSiteId;
    }

    public function cemeteryBlockId(): CemeteryBlockId
    {
        return $this->cemeteryBlockId;
    }

    public function rowInBlock(): RowInBlock
    {
        return $this->rowInBlock;
    }

    public function positionInRow(): ?PositionInRow
    {
        return $this->positionInRow;
    }
}
