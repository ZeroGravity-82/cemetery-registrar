<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\Columbarium;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumName;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\Error;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use Cemetery\Tests\Registrar\Domain\AggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumTest extends AggregateRootTest
{
    private ColumbariumId   $id;
    private ColumbariumName $name;
    private Columbarium     $columbarium;

    public function setUp(): void
    {
        $this->id          = new ColumbariumId('C001');
        $this->name        = new ColumbariumName('западный');
        $this->columbarium = new Columbarium($this->id, $this->name);
        $this->entity      = $this->columbarium;
    }

    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(ColumbariumId::class, $this->columbarium->id());
        $this->assertTrue($this->columbarium->id()->isEqual($this->id));
        $this->assertInstanceOf(ColumbariumName::class, $this->columbarium->name());
        $this->assertTrue($this->columbarium->name()->isEqual($this->name));
        $this->assertNull($this->columbarium->geoPosition());
    }

    public function testItSetsName(): void
    {
        $name = new ColumbariumName('восточный');
        $this->columbarium->setName($name);
        $this->assertInstanceOf(ColumbariumName::class, $this->columbarium->name());
        $this->assertTrue($this->columbarium->name()->isEqual($name));
    }

    public function testItSetsGeoPosition(): void
    {
        $geoPosition = new GeoPosition(new Coordinates('54.950357', '82.7972252'), new Error('0.5'));
        $this->columbarium->setGeoPosition($geoPosition);
        $this->assertInstanceOf(GeoPosition::class, $this->columbarium->geoPosition());
        $this->assertTrue($this->columbarium->geoPosition()->isEqual($geoPosition));
    }
}
