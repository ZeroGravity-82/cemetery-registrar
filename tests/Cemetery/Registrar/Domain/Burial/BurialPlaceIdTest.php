<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\BurialPlace\ColumbariumNicheId;
use Cemetery\Registrar\Domain\BurialPlace\GraveSiteId;
use Cemetery\Registrar\Domain\BurialPlace\MemorialTreeId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialPlaceId = new BurialPlaceId(new GraveSiteId('GS001'));
        $this->assertInstanceOf(GraveSiteId::class, $burialPlaceId->getId());
        $this->assertSame('GS001', $burialPlaceId->getId()->getValue());

        $burialPlaceId = new BurialPlaceId(new ColumbariumNicheId('CN001'));
        $this->assertInstanceOf(ColumbariumNicheId::class, $burialPlaceId->getId());
        $this->assertSame('CN001', $burialPlaceId->getId()->getValue());

        $burialPlaceId = new BurialPlaceId(new MemorialTreeId('MT001'));
        $this->assertInstanceOf(MemorialTreeId::class, $burialPlaceId->getId());
        $this->assertSame('MT001', $burialPlaceId->getId()->getValue());
    }

    public function testItStringifyable(): void
    {
        $burialPlaceId        = new BurialPlaceId(new GraveSiteId('GS001'));
        $decodedBurialPlaceId = \json_decode((string) $burialPlaceId, true);
        $this->assertIsArray($decodedBurialPlaceId);
        $this->assertArrayHasKey('type', $decodedBurialPlaceId);
        $this->assertArrayHasKey('value', $decodedBurialPlaceId);
        $this->assertSame('GraveSiteId', $decodedBurialPlaceId['type']);
        $this->assertSame('GS001', $decodedBurialPlaceId['value']);

        $burialPlaceId        = new BurialPlaceId(new ColumbariumNicheId('CN001'));
        $decodedBurialPlaceId = \json_decode((string) $burialPlaceId, true);
        $this->assertIsArray($decodedBurialPlaceId);
        $this->assertArrayHasKey('type', $decodedBurialPlaceId);
        $this->assertArrayHasKey('value', $decodedBurialPlaceId);
        $this->assertSame('ColumbariumNicheId', $decodedBurialPlaceId['type']);
        $this->assertSame('CN001', $decodedBurialPlaceId['value']);

        $burialPlaceId        = new BurialPlaceId(new MemorialTreeId('MT001'));
        $decodedBurialPlaceId = \json_decode((string) $burialPlaceId, true);
        $this->assertIsArray($decodedBurialPlaceId);
        $this->assertArrayHasKey('type', $decodedBurialPlaceId);
        $this->assertArrayHasKey('value', $decodedBurialPlaceId);
        $this->assertSame('MemorialTreeId', $decodedBurialPlaceId['type']);
        $this->assertSame('MT001', $decodedBurialPlaceId['value']);
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
