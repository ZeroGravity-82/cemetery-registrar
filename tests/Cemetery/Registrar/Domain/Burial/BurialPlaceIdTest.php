<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceId;
use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialPlaceType = new BurialPlaceType(BurialPlaceType::GRAVE_SITE);
        $burialPlaceId   = new BurialPlaceId('777', $burialPlaceType);
        $this->assertSame('777', $burialPlaceId->getValue());
        $this->assertSame($burialPlaceType, $burialPlaceId->getType());
    }

    public function testItStringifyable(): void
    {
        $burialPlaceType = new BurialPlaceType(BurialPlaceType::GRAVE_SITE);
        $burialPlaceId   = new BurialPlaceId('777', $burialPlaceType);
        $this->assertSame(BurialPlaceType::GRAVE_SITE . '.' . '777', (string) $burialPlaceId);
    }

    public function testItComparable(): void
    {
        $burialPlaceIdA = new BurialPlaceId('777', new BurialPlaceType(BurialPlaceType::MEMORIAL_TREE));
        $burialPlaceIdB = new BurialPlaceId('777', new BurialPlaceType(BurialPlaceType::GRAVE_SITE));
        $burialPlaceIdC = new BurialPlaceId('888', new BurialPlaceType(BurialPlaceType::COLUMBARIUM_NICHE));
        $burialPlaceIdD = new BurialPlaceId('777', new BurialPlaceType(BurialPlaceType::MEMORIAL_TREE));

        $this->assertFalse($burialPlaceIdA->isEqual($burialPlaceIdB));
        $this->assertFalse($burialPlaceIdA->isEqual($burialPlaceIdC));
        $this->assertTrue($burialPlaceIdA->isEqual($burialPlaceIdD));
        $this->assertFalse($burialPlaceIdB->isEqual($burialPlaceIdC));
        $this->assertFalse($burialPlaceIdB->isEqual($burialPlaceIdD));
        $this->assertFalse($burialPlaceIdC->isEqual($burialPlaceIdD));
    }
}
