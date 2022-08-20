<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceIdFactory;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdFactoryTest extends TestCase
{
    private BurialPlaceIdFactory $burialPlaceIdFactory;

    public function setUp(): void
    {
        $this->burialPlaceIdFactory = new BurialPlaceIdFactory();
    }

    public function testItCreatesBurialPlaceId(): void
    {
        $burialPlaceId = $this->burialPlaceIdFactory->create(new GraveSiteId('ID001'));
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(GraveSiteId::class, $burialPlaceId->id());
        $this->assertSame('ID001', $burialPlaceId->id()->value());

        $burialPlaceId = $this->burialPlaceIdFactory->create(new ColumbariumNicheId('ID002'));
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(ColumbariumNicheId::class, $burialPlaceId->id());
        $this->assertSame('ID002', $burialPlaceId->id()->value());

        $burialPlaceId = $this->burialPlaceIdFactory->create(new MemorialTreeId('ID003'));
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(MemorialTreeId::class, $burialPlaceId->id());
        $this->assertSame('ID003', $burialPlaceId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForGraveSite(): void
    {
        $burialPlaceId = $this->burialPlaceIdFactory->createForGraveSite('ID004');
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(GraveSiteId::class, $burialPlaceId->id());
        $this->assertSame('ID004', $burialPlaceId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForColumbariumNiche(): void
    {
        $burialPlaceId = $this->burialPlaceIdFactory->createForColumbariumNiche('ID005');
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(ColumbariumNicheId::class, $burialPlaceId->id());
        $this->assertSame('ID005', $burialPlaceId->id()->value());
    }

    public function testItCreatesBurialPlaceIdForMemorialTree(): void
    {
        $burialPlaceId = $this->burialPlaceIdFactory->createForMemorialTree('ID006');
        $this->assertInstanceOf(BurialPlaceId::class, $burialPlaceId);
        $this->assertInstanceOf(MemorialTreeId::class, $burialPlaceId->id());
        $this->assertSame('ID006', $burialPlaceId->id()->value());
    }
}
