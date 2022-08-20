<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\BurialPlace;

use Cemetery\Registrar\Domain\Model\BurialPlace\BurialPlaceId;
use Cemetery\Registrar\Domain\Model\BurialPlace\ColumbariumNiche\ColumbariumNicheId;
use Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite\GraveSiteId;
use Cemetery\Registrar\Domain\Model\BurialPlace\MemorialTree\MemorialTreeId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialPlaceId = new BurialPlaceId(new GraveSiteId('GS001'));
        $this->assertInstanceOf(GraveSiteId::class, $burialPlaceId->id());
        $this->assertSame('GS001', $burialPlaceId->id()->value());

        $burialPlaceId = new BurialPlaceId(new ColumbariumNicheId('CN001'));
        $this->assertInstanceOf(ColumbariumNicheId::class, $burialPlaceId->id());
        $this->assertSame('CN001', $burialPlaceId->id()->value());

        $burialPlaceId = new BurialPlaceId(new MemorialTreeId('MT001'));
        $this->assertInstanceOf(MemorialTreeId::class, $burialPlaceId->id());
        $this->assertSame('MT001', $burialPlaceId->id()->value());
    }

    public function testItComparable(): void
    {
        $burialPlaceIdA = new BurialPlaceId(new GraveSiteId('ID001'));
        $burialPlaceIdB = new BurialPlaceId(new ColumbariumNicheId('ID001'));
        $burialPlaceIdC = new BurialPlaceId(new GraveSiteId('ID002'));
        $burialPlaceIdD = new BurialPlaceId(new MemorialTreeId('ID003'));
        $burialPlaceIdE = new BurialPlaceId(new GraveSiteId('ID001'));

        $this->assertFalse($burialPlaceIdA->isEqual($burialPlaceIdB));
        $this->assertFalse($burialPlaceIdA->isEqual($burialPlaceIdC));
        $this->assertFalse($burialPlaceIdA->isEqual($burialPlaceIdD));
        $this->assertTrue($burialPlaceIdA->isEqual($burialPlaceIdE));
        $this->assertFalse($burialPlaceIdB->isEqual($burialPlaceIdC));
        $this->assertFalse($burialPlaceIdB->isEqual($burialPlaceIdD));
        $this->assertFalse($burialPlaceIdB->isEqual($burialPlaceIdE));
        $this->assertFalse($burialPlaceIdC->isEqual($burialPlaceIdD));
        $this->assertFalse($burialPlaceIdC->isEqual($burialPlaceIdE));
        $this->assertFalse($burialPlaceIdD->isEqual($burialPlaceIdE));
    }
}
