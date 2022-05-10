<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialPlace\ColumbariumNiche;

use Cemetery\Registrar\Domain\IdentityGenerator;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNiche;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ColumbariumNicheFactoryTest extends TestCase
{
    private MockObject|IdentityGenerator $mockIdentityGenerator;
    private ColumbariumNicheFactory      $columbariumNicheFactory;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn('555');

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
            $geoPositionError
        );
        $this->assertInstanceOf(ColumbariumNiche::class, $columbariumNiche);
        $this->assertSame('555', $columbariumNiche->id()->value());
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
            null
        );
        $this->assertInstanceOf(ColumbariumNiche::class, $columbariumNiche);
        $this->assertSame('555', $columbariumNiche->id()->value());
        $this->assertSame($columbariumId, $columbariumNiche->columbariumId()->value());
        $this->assertSame($rowInColumbarium, $columbariumNiche->rowInColumbarium()->value());
        $this->assertSame($nicheNumber, $columbariumNiche->nicheNumber()->value());
        $this->assertNull($columbariumNiche->geoPosition());
    }

    public function testItFailsToCreateColumbariumNicheWithoutColumbariumId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Идентификатор доменной сущности не может иметь пустое значение.');
        $rowInColumbarium = 7;
        $nicheNumber      = '001';
        $this->columbariumNicheFactory->create(
            null,
            $rowInColumbarium,
            $nicheNumber,
            null,
            null,
            null
        );
    }

    public function testItFailsToCreateColumbariumNicheWithoutRowInColumbarium(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Ряд в колумбарии не может иметь нулевое значение.');
        $columbariumId = 'C001';
        $nicheNumber   = '001';
        $this->columbariumNicheFactory->create(
            $columbariumId,
            null,
            $nicheNumber,
            null,
            null,
            null
        );
    }

    public function testItFailsToCreateColumbariumNicheWithoutNicheNumber(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Номер колумбарной ниши не может иметь пустое значение.');
        $columbariumId    = 'C001';
        $rowInColumbarium = 7;
        $this->columbariumNicheFactory->create(
            $columbariumId,
            $rowInColumbarium,
            null,
            null,
            null,
            null
        );
    }
}
