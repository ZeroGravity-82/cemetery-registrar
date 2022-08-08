<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteRemoved;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Tests\Registrar\Domain\Model\EventTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteRemovedTest extends EventTest
{
    private GraveSiteId       $graveSiteId;
    private CemeteryBlockId $cemeteryBlockId;
    private RowInBlock        $rowInBlock;
    private PositionInRow     $positionInRow;

    public function setUp(): void
    {
        $this->graveSiteId     = new GraveSiteId('GS001');
        $this->cemeteryBlockId = new CemeteryBlockId('CB001');
        $this->rowInBlock      = new RowInBlock(5);
        $this->positionInRow   = new PositionInRow(10);
        $this->event           = new GraveSiteRemoved(
            $this->graveSiteId,
            $this->cemeteryBlockId,
            $this->rowInBlock,
            $this->positionInRow,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertTrue($this->graveSiteId->isEqual($this->event->graveSiteId()));
        $this->assertTrue($this->cemeteryBlockId->isEqual($this->event->cemeteryBlockId()));
        $this->assertTrue($this->rowInBlock->isEqual($this->event->rowInBlock()));
        $this->assertTrue($this->positionInRow->isEqual($this->event->positionInRow()));
    }
}
