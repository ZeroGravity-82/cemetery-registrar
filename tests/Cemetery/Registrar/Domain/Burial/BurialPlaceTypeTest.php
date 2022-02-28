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
            'Неподдерживаемый тип места захоронения "неподдерживаемый_тип", должен быть один из "%s", "%s", "%s".',
            BurialPlaceType::GRAVE_SITE,
            BurialPlaceType::COLUMBARIUM_NICHE,
            BurialPlaceType::MEMORIAL_TREE,
        ));
        new BurialPlaceType('неподдерживаемый_тип');
    }

    public function testItStringifyable(): void
    {
        $burialPlaceType = BurialPlaceType::graveSite();

        $this->assertSame(BurialPlaceType::GRAVE_SITE, (string) $burialPlaceType);
    }

    public function testItComparable(): void
    {
        $burialPlaceTypeA = BurialPlaceType::graveSite();
        $burialPlaceTypeB = BurialPlaceType::columbariumNiche();
        $burialPlaceTypeC = BurialPlaceType::memorialTree();
        $burialPlaceTypeD = BurialPlaceType::graveSite();

        $this->assertFalse($burialPlaceTypeA->isEqual($burialPlaceTypeB));
        $this->assertFalse($burialPlaceTypeA->isEqual($burialPlaceTypeC));
        $this->assertTrue($burialPlaceTypeA->isEqual($burialPlaceTypeD));
        $this->assertFalse($burialPlaceTypeB->isEqual($burialPlaceTypeC));
        $this->assertFalse($burialPlaceTypeB->isEqual($burialPlaceTypeD));
        $this->assertFalse($burialPlaceTypeC->isEqual($burialPlaceTypeD));
    }
}
