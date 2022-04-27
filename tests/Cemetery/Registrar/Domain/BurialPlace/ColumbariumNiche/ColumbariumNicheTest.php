<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheTest extends AbstractAggregateRootTest
{
    private ColumbariumNicheId     $id;
    private ColumbariumId          $columbariumId;
    private RowInColumbarium       $rowInColumbarium;
    private ColumbariumNicheNumber $nicheNumber;
    private ColumbariumNiche       $columbariumNiche;

    public function setUp(): void
    {
        $this->id               = new ColumbariumNicheId('CN001');
        $this->columbariumId    = new ColumbariumId('C001');
        $this->rowInColumbarium = new RowInColumbarium(4);
        $this->nicheNumber      = new ColumbariumNicheNumber('001');
        $this->columbariumNiche = new ColumbariumNiche(
            $this->id,
            $this->columbariumId,
            $this->rowInColumbarium,
            $this->nicheNumber
        );
        $this->entity = $this->columbariumNiche;
    }

    public function testItHasValidLabelConstant(): void
    {
        $this->assertSame('колумбарная ниша', ColumbariumNiche::LABEL);
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(ColumbariumNicheId::class, $this->columbariumNiche->id());
        $this->assertTrue($this->columbariumNiche->id()->isEqual($this->id));
        $this->assertInstanceOf(ColumbariumId::class, $this->columbariumNiche->columbariumId());
        $this->assertTrue($this->columbariumNiche->columbariumId()->isEqual($this->columbariumId));
        $this->assertInstanceOf(RowInColumbarium::class, $this->columbariumNiche->rowInColumbarium());
        $this->assertTrue($this->columbariumNiche->rowInColumbarium()->isEqual($this->rowInColumbarium));
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $this->columbariumNiche->nicheNumber());
        $this->assertTrue($this->columbariumNiche->nicheNumber()->isEqual($this->nicheNumber));
        $this->assertNull($this->columbariumNiche->geoPosition());
    }

    public function testItSetsColumbariumId(): void
    {
        $columbariumId = new ColumbariumId('C002');
        $this->columbariumNiche->setColumbariumId($columbariumId);
        $this->assertInstanceOf(ColumbariumId::class, $this->columbariumNiche->columbariumId());
        $this->assertTrue($this->columbariumNiche->columbariumId()->isEqual($columbariumId));
    }

    public function testItSetsRowInColumbarium(): void
    {
        $rowInColumbarium = new RowInColumbarium(5);
        $this->columbariumNiche->setRowInColumbarium($rowInColumbarium);
        $this->assertInstanceOf(RowInColumbarium::class, $this->columbariumNiche->rowInColumbarium());
        $this->assertTrue($this->columbariumNiche->rowInColumbarium()->isEqual($rowInColumbarium));
    }

    public function testItSetsNicheNumber(): void
    {
        $nicheNumber = new ColumbariumNicheNumber('002');
        $this->columbariumNiche->setNicheNumber($nicheNumber);
        $this->assertInstanceOf(ColumbariumNicheNumber::class, $this->columbariumNiche->nicheNumber());
        $this->assertTrue($this->columbariumNiche->nicheNumber()->isEqual($nicheNumber));
    }

    public function testItSetsGeoPosition(): void
    {
        $geoPosition = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Accuracy('0.5'));
        $this->columbariumNiche->setGeoPosition($geoPosition);
        $this->assertInstanceOf(GeoPosition::class, $this->columbariumNiche->geoPosition());
        $this->assertTrue($this->columbariumNiche->geoPosition()->isEqual($geoPosition));
    }
}