<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\BurialPlace\CemeteryBlockId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteTest extends AbstractAggregateRootTest
{
    private GraveSite $graveSite;

    public function setUp(): void
    {
        $id               = new GraveSiteId('GS001');
        $cemeteryBlockId  = new CemeteryBlockId('CB001');
        $rowInBlock       = new RowInBlock(5);
        $this->graveSite  = new GraveSite($id, $cemeteryBlockId, $rowInBlock);
        $this->entity     = $this->graveSite;
    }

    public function testItHasValidLabelConstant(): void
    {
        $this->assertSame('участок на кладбище', GraveSite::LABEL);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(GraveSiteId::class, $this->graveSite->id());
        $this->assertSame('GS001', $this->graveSite->id()->value());
        $this->assertInstanceOf(CemeteryBlockId::class, $this->graveSite->cemeteryBlockId());
        $this->assertSame('CB001', $this->graveSite->cemeteryBlockId()->value());
        $this->assertInstanceOf(RowInBlock::class, $this->graveSite->rowInBlock());
        $this->assertSame(5, $this->graveSite->rowInBlock()->value());
        $this->assertNull($this->graveSite->positionInRow());
        $this->assertNull($this->graveSite->geoPosition());
        $this->assertNull($this->graveSite->size());
    }

    public function testItSetsCemeteryBlockId(): void
    {
        $cemeteryBlockId = new CemeteryBlockId('CB002');
        $this->graveSite->setCemeteryBlockId($cemeteryBlockId);
        $this->assertTrue($this->graveSite->cemeteryBlockId()->isEqual($cemeteryBlockId));
    }

    public function testItSetsRowInBlock(): void
    {
        $rowInBlock = new RowInBlock(6);
        $this->graveSite->setRowInBlock($rowInBlock);
        $this->assertTrue($this->graveSite->rowInBlock()->isEqual($rowInBlock));
    }

    public function testItSetsPositionInRow(): void
    {
        $positionInRow = new PositionInRow(10);
        $this->graveSite->setPositionInRow($positionInRow);
        $this->assertTrue($this->graveSite->positionInRow()->isEqual($positionInRow));
    }

    public function testItSetsGeoPosition(): void
    {
        $geoPosition = new GeoPosition(new Coordinates('54.9472658', '82.8043771'), new Accuracy('0.25'));
        $this->graveSite->setGeoPosition($geoPosition);
        $this->assertTrue($this->graveSite->geoPosition()->isEqual($geoPosition));
    }

    public function testItSetsSize(): void
    {
        $size = new GraveSiteSize('2.5');
        $this->graveSite->setSize($size);
        $this->assertTrue($this->graveSite->size()->isEqual($size));
    }
}
