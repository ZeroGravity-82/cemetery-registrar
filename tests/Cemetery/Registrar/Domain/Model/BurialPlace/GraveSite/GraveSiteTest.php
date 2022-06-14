<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\CemeteryBlockId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteSize;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\PositionInRow;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\RowInBlock;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\Model\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteTest extends AggregateRootTest
{
    private GraveSiteId     $id;
    private CemeteryBlockId $cemeteryBlockId;
    private RowInBlock      $rowInBlock;
    private GraveSite       $graveSite;

    public function setUp(): void
    {
        $this->id              = new GraveSiteId('GS001');
        $this->cemeteryBlockId = new CemeteryBlockId('CB001');
        $this->rowInBlock      = new RowInBlock(5);
        $this->graveSite       = new GraveSite($this->id, $this->cemeteryBlockId, $this->rowInBlock);
        $this->entity          = $this->graveSite;
    }

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('GRAVE_SITE', GraveSite::CLASS_SHORTCUT);
    }

    public function testItHasValidClassLabelConstant(): void
    {
        $this->assertSame('участок на кладбище', GraveSite::CLASS_LABEL);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(GraveSiteId::class, $this->graveSite->id());
        $this->assertTrue($this->graveSite->id()->isEqual($this->id));
        $this->assertInstanceOf(CemeteryBlockId::class, $this->graveSite->cemeteryBlockId());
        $this->assertTrue($this->graveSite->cemeteryBlockId()->isEqual($this->cemeteryBlockId));
        $this->assertInstanceOf(RowInBlock::class, $this->graveSite->rowInBlock());
        $this->assertTrue($this->graveSite->rowInBlock()->isEqual($this->rowInBlock));
        $this->assertNull($this->graveSite->positionInRow());
        $this->assertNull($this->graveSite->geoPosition());
        $this->assertNull($this->graveSite->size());
    }

    public function testItSetsCemeteryBlockId(): void
    {
        $cemeteryBlockId = new CemeteryBlockId('CB002');
        $this->graveSite->setCemeteryBlockId($cemeteryBlockId);
        $this->assertInstanceOf(CemeteryBlockId::class, $this->graveSite->cemeteryBlockId());
        $this->assertTrue($this->graveSite->cemeteryBlockId()->isEqual($cemeteryBlockId));
    }

    public function testItSetsRowInBlock(): void
    {
        $rowInBlock = new RowInBlock(6);
        $this->graveSite->setRowInBlock($rowInBlock);
        $this->assertInstanceOf(RowInBlock::class, $this->graveSite->rowInBlock());
        $this->assertTrue($this->graveSite->rowInBlock()->isEqual($rowInBlock));
    }

    public function testItSetsPositionInRow(): void
    {
        $positionInRow = new PositionInRow(10);
        $this->graveSite->setPositionInRow($positionInRow);
        $this->assertInstanceOf(PositionInRow::class, $this->graveSite->positionInRow());
        $this->assertTrue($this->graveSite->positionInRow()->isEqual($positionInRow));
    }

    public function testItSetsGeoPosition(): void
    {
        $geoPosition = new GeoPosition(new Coordinates('54.9472658', '82.8043771'), new Error('0.25'));
        $this->graveSite->setGeoPosition($geoPosition);
        $this->assertInstanceOf(GeoPosition::class, $this->graveSite->geoPosition());
        $this->assertTrue($this->graveSite->geoPosition()->isEqual($geoPosition));
    }

    public function testItSetsSize(): void
    {
        $size = new GraveSiteSize('2.5');
        $this->graveSite->setSize($size);
        $this->assertInstanceOf(GraveSiteSize::class, $this->graveSite->size());
        $this->assertTrue($this->graveSite->size()->isEqual($size));
    }
}
