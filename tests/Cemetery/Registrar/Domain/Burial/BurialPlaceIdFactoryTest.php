<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceIdFactory;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTree\MemorialTreeId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdFactoryTest extends TestCase
{
    public function testItCreatesBurialPlaceIdForGraveSite(): void
    {
        $burialPlaceIdFactory = new BurialPlaceIdFactory();

        $burialPlaceId = $burialPlaceIdFactory->createForGraveSite('ID001');
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(GraveSiteId::class, $burialPlaceId->id());
        $this->assertSame('ID001', $burialPlaceId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForColumbariumNiche(): void
    {
        $burialPlaceIdFactory = new BurialPlaceIdFactory();

        $burialPlaceId = $burialPlaceIdFactory->createForColumbariumNiche('ID002');
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(ColumbariumNicheId::class, $burialPlaceId->id());
        $this->assertSame('ID002', $burialPlaceId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForMemorialTree(): void
    {
        $burialPlaceIdFactory = new BurialPlaceIdFactory();

        $burialPlaceId = $burialPlaceIdFactory->createForMemorialTree('ID003');
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(MemorialTreeId::class, $burialPlaceId->id());
        $this->assertSame('ID003', $burialPlaceId->id()->value());
    }
}
