<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheFactory;
use Cemetery\Tests\Registrar\Domain\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheFactoryTest extends EntityFactoryTest
{
    private ColumbariumNicheFactory $columbariumNicheFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->columbariumNicheFactory = new ColumbariumNicheFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesColumbariumNiche(): void
    {
        $columbariumId        = 'C001';
        $rowInColumbarium     = 7;
        $nicheNumber          = '001';
        $geoPositionLatitude  = '54.950357';
        $geoPositionLongitude = '82.7972252';
        $geoPositionError     = '0.2';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $columbariumNiche = $this->columbariumNicheFactory->create(
            $columbariumId,
            $rowInColumbarium,
            $nicheNumber,
            $geoPositionLatitude,
            $geoPositionLongitude,
            $geoPositionError,
        );
        $this->assertInstanceOf(ColumbariumNiche::class, $columbariumNiche);
        $this->assertSame(self::ENTITY_ID, $columbariumNiche->id()->value());
        $this->assertSame($columbariumId, $columbariumNiche->columbariumId()->value());
        $this->assertSame($rowInColumbarium, $columbariumNiche->rowInColumbarium()->value());
        $this->assertSame($nicheNumber, $columbariumNiche->nicheNumber()->value());
        $this->assertSame($geoPositionLatitude, $columbariumNiche->geoPosition()->coordinates()->latitude());
        $this->assertSame($geoPositionLongitude, $columbariumNiche->geoPosition()->coordinates()->longitude());
        $this->assertSame($geoPositionError, $columbariumNiche->geoPosition()->error()->value());
    }

    public function testItCreatesColumbariumNicheWithoutOptionalFields(): void
    {
        $columbariumId    = 'C001';
        $rowInColumbarium = 7;
        $nicheNumber      = '001';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $columbariumNiche = $this->columbariumNicheFactory->create(
            $columbariumId,
            $rowInColumbarium,
            $nicheNumber,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(ColumbariumNiche::class, $columbariumNiche);
        $this->assertSame(self::ENTITY_ID, $columbariumNiche->id()->value());
        $this->assertSame($columbariumId, $columbariumNiche->columbariumId()->value());
        $this->assertSame($rowInColumbarium, $columbariumNiche->rowInColumbarium()->value());
        $this->assertSame($nicheNumber, $columbariumNiche->nicheNumber()->value());
        $this->assertNull($columbariumNiche->geoPosition());
    }
}
