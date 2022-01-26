<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\BurialType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $burialType = new BurialType(BurialType::COFFIN_IN_GRAVE_SITE);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE, $burialType->getValue());
        $this->assertTrue($burialType->isCoffinInGraveSite());
        $this->assertFalse($burialType->isUrnInGraveSite());
        $this->assertFalse($burialType->isUrnInColumbariumNiche());
        $this->assertFalse($burialType->isAshesUnderMemorialTree());

        $burialType = BurialType::urnInColumbariumNiche();
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM_NICHE, $burialType->getValue());
        $this->assertFalse($burialType->isCoffinInGraveSite());
        $this->assertFalse($burialType->isUrnInGraveSite());
        $this->assertTrue($burialType->isUrnInColumbariumNiche());
        $this->assertFalse($burialType->isAshesUnderMemorialTree());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Unsupported burial type "wrong_type", expected to be one of "%s", "%s", "%s", "%s".',
            BurialType::COFFIN_IN_GRAVE_SITE,
            BurialType::URN_IN_GRAVE_SITE,
            BurialType::URN_IN_COLUMBARIUM_NICHE,
            BurialType::ASHES_UNDER_MEMORIAL_TREE,
        ));
        new BurialType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $burialType = BurialType::coffinInGraveSite();

        $this->assertSame(BurialType::COFFIN_IN_GRAVE_SITE, (string) $burialType);
    }

    public function testItComparable(): void
    {
        $burialTypeA = BurialType::coffinInGraveSite();
        $burialTypeB = BurialType::urnInGraveSite();
        $burialTypeC = BurialType::urnInColumbariumNiche();
        $burialTypeD = BurialType::ashesUnderMemorialTree();
        $burialTypeE = BurialType::coffinInGraveSite();

        $this->assertFalse($burialTypeA->isEqual($burialTypeB));
        $this->assertFalse($burialTypeA->isEqual($burialTypeC));
        $this->assertFalse($burialTypeA->isEqual($burialTypeD));
        $this->assertTrue($burialTypeA->isEqual($burialTypeE));
        $this->assertFalse($burialTypeB->isEqual($burialTypeC));
        $this->assertFalse($burialTypeB->isEqual($burialTypeD));
        $this->assertFalse($burialTypeC->isEqual($burialTypeD));
    }
}
