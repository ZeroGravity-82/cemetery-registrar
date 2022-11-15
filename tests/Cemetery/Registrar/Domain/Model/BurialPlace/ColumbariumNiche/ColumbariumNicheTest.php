<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheNumber;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\RowInColumbarium;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\Model\BurialPlace\AbstractBurialPlaceTest;
use DataFixtures\BurialPlace\ColumbariumNiche\ColumbariumProvider;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheTest extends AbstractBurialPlaceTest
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
        $this->burialPlace = $this->columbariumNiche;
        $this->entity      = $this->columbariumNiche;
    }

    public function testItHasValidClassShortcutConstant(): void
    {
        $this->assertSame('COLUMBARIUM_NICHE', ColumbariumNiche::CLASS_SHORTCUT);
    }

    public function testItHasValidClassLabelConstant(): void
    {
        $this->assertSame('колумбарная ниша', ColumbariumNiche::CLASS_LABEL);
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
        $columbarium = ColumbariumProvider::getColumbariumB();
        $this->columbariumNiche->setColumbarium($columbarium);
        $this->assertInstanceOf(ColumbariumId::class, $this->columbariumNiche->columbariumId());
        $this->assertTrue($this->columbariumNiche->columbariumId()->isEqual($columbarium->id()));
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
        $geoPosition = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Error('0.5'));
        $this->columbariumNiche->setGeoPosition($geoPosition);
        $this->assertInstanceOf(GeoPosition::class, $this->columbariumNiche->geoPosition());
        $this->assertTrue($this->columbariumNiche->geoPosition()->isEqual($geoPosition));
    }
}
