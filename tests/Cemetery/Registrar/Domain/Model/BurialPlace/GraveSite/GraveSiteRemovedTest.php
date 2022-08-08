<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockName;
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
    private CemeteryBlockName $cemeteryBlockName;
    private RowInBlock        $rowInBlock;
    private PositionInRow     $positionInRow;

    public function setUp(): void
    {
        $this->graveSiteId       = new GraveSiteId('CB001');
        $this->cemeteryBlockName = new CemeteryBlockName('южный');
        $this->rowInBlock        = new RowInBlock(5);
        $this->positionInRow     = new PositionInRow(10);
        $this->event             = new GraveSiteRemoved(
            $this->graveSiteId,
            $this->cemeteryBlockName,
            $this->rowInBlock,
            $this->positionInRow,
        );
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertSame($this->graveSiteId, $this->event->graveSiteId());
        $this->assertSame($this->cemeteryBlockName, $this->event->cemeteryBlockName());
        $this->assertSame($this->rowInBlock, $this->event->rowInBlock());
        $this->assertSame($this->positionInRow, $this->event->positionInRow());
    }
}
