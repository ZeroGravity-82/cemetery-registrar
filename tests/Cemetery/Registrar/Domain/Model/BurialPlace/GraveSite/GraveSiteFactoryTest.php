<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSite;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteFactory;
use Cemetery\Tests\Registrar\Domain\Model\EntityFactoryTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GraveSiteFactoryTest extends EntityFactoryTest
{
    private GraveSiteFactory $graveSiteFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->graveSiteFactory = new GraveSiteFactory($this->mockIdentityGenerator);
    }

    public function testItCreatesColumbariumNiche(): void
    {
        $cemeteryBlockId      = 'CB01';
        $rowInBlock           = 5;
        $positionInRow        = 10;
        $geoPositionLatitude  = '54.950357';
        $geoPositionLongitude = '82.7972252';
        $geoPositionError     = '0.2';
        $size                 = '3.0';
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $graveSite = $this->graveSiteFactory->create(
            $cemeteryBlockId,
            $rowInBlock,
            $positionInRow,
            $geoPositionLatitude,
            $geoPositionLongitude,
            $geoPositionError,
            $size,
        );
        $this->assertInstanceOf(GraveSite::class, $graveSite);
        $this->assertSame(self::ENTITY_ID, $graveSite->id()->value());
        $this->assertSame($cemeteryBlockId, $graveSite->cemeteryBlockId()->value());
        $this->assertSame($rowInBlock, $graveSite->rowInBlock()->value());
        $this->assertSame($positionInRow, $graveSite->positionInRow()->value());
        $this->assertSame($geoPositionLatitude, $graveSite->geoPosition()->coordinates()->latitude());
        $this->assertSame($geoPositionLongitude, $graveSite->geoPosition()->coordinates()->longitude());
        $this->assertSame($geoPositionError, $graveSite->geoPosition()->error()->value());
        $this->assertSame($size, $graveSite->size()->value());
    }

    public function testItCreatesColumbariumNicheWithoutOptionalFields(): void
    {
        $cemeteryBlockId = 'CB01';
        $rowInBlock      = 5;
        $this->mockIdentityGenerator->expects($this->once())->method('getNextIdentity');
        $graveSite = $this->graveSiteFactory->create(
            $cemeteryBlockId,
            $rowInBlock,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(GraveSite::class, $graveSite);
        $this->assertSame(self::ENTITY_ID, $graveSite->id()->value());
        $this->assertSame($cemeteryBlockId, $graveSite->cemeteryBlockId()->value());
        $this->assertSame($rowInBlock, $graveSite->rowInBlock()->value());
        $this->assertNull($graveSite->positionInRow());
        $this->assertNull($graveSite->geoPosition());
        $this->assertNull($graveSite->size());
    }
}
