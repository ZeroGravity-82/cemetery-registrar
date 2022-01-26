<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialPlaceType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialPlaceTypeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialPlaceType = new BurialPlaceType(BurialPlaceType::GRAVE_SITE);
        $this->assertSame(BurialPlaceType::GRAVE_SITE, $burialPlaceType->getValue());
        $this->assertTrue($burialPlaceType->isGraveSite());
        $this->assertFalse($burialPlaceType->isColumbariumNiche());
        $this->assertFalse($burialPlaceType->isMemorialTree());

        $burialPlaceType = BurialPlaceType::columbariumNiche();
        $this->assertSame(BurialPlaceType::COLUMBARIUM_NICHE, $burialPlaceType->getValue());
        $this->assertFalse($burialPlaceType->isGraveSite());
        $this->assertTrue($burialPlaceType->isColumbariumNiche());
        $this->assertFalse($burialPlaceType->isMemorialTree());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Unsupported burial place type "wrong_type", expected to be one of "%s", "%s", "%s".',
            BurialPlaceType::GRAVE_SITE,
            BurialPlaceType::COLUMBARIUM_NICHE,
            BurialPlaceType::MEMORIAL_TREE,
        ));
        new BurialPlaceType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $burialPlaceType = BurialPlaceType::graveSite();

        $this->assertSame(BurialPlaceType::GRAVE_SITE, (string) $burialPlaceType);
    }

    public function testItComparable(): void
    {
        $burialPlaceTypeGraveSiteA       = BurialPlaceType::graveSite();
        $burialPlaceTypeColumbariumNiche = BurialPlaceType::columbariumNiche();
        $burialPlaceTypeMemorialTree     = BurialPlaceType::memorialTree();
        $burialPlaceTypeGraveSiteB       = BurialPlaceType::graveSite();

        $this->assertFalse($burialPlaceTypeGraveSiteA->isEqual($burialPlaceTypeColumbariumNiche));
        $this->assertFalse($burialPlaceTypeGraveSiteA->isEqual($burialPlaceTypeMemorialTree));
        $this->assertTrue($burialPlaceTypeGraveSiteA->isEqual($burialPlaceTypeGraveSiteB));
        $this->assertFalse($burialPlaceTypeColumbariumNiche->isEqual($burialPlaceTypeMemorialTree));
        $this->assertFalse($burialPlaceTypeColumbariumNiche->isEqual($burialPlaceTypeGraveSiteB));
        $this->assertFalse($burialPlaceTypeMemorialTree->isEqual($burialPlaceTypeGraveSiteB));
    }
}
