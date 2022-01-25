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
        $burialType = new BurialType(BurialType::COFFIN_IN_GRAVE);
        $this->assertSame(BurialType::COFFIN_IN_GRAVE, $burialType->getValue());
        $this->assertTrue($burialType->isCoffinInGrave());
        $this->assertFalse($burialType->isUrnInGrave());
        $this->assertFalse($burialType->isUrnInColumbarium());
        $this->assertFalse($burialType->isAshesUnderTree());

        $burialType = BurialType::urnInColumbarium();
        $this->assertSame(BurialType::URN_IN_COLUMBARIUM, $burialType->getValue());
        $this->assertFalse($burialType->isCoffinInGrave());
        $this->assertFalse($burialType->isUrnInGrave());
        $this->assertTrue($burialType->isUrnInColumbarium());
        $this->assertFalse($burialType->isAshesUnderTree());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Unsupported burial type "wrong_type", expected to be one of "%s", "%s", "%s", "%s".',
            BurialType::COFFIN_IN_GRAVE,
            BurialType::URN_IN_GRAVE,
            BurialType::URN_IN_COLUMBARIUM,
            BurialType::ASHES_UNDER_TREE,
        ));
        new BurialType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $burialType = BurialType::coffinInGrave();

        $this->assertSame(BurialType::COFFIN_IN_GRAVE, (string) $burialType);
    }

    public function testItComparable(): void
    {
        $burialTypeCoffinInGraveA   = BurialType::coffinInGrave();
        $burialTypeUrnInGround      = BurialType::urnInGrave();
        $burialTypeUrnInColumbarium = BurialType::urnInColumbarium();
        $burialTypeAshesUnderTree   = BurialType::ashesUnderTree();
        $burialTypeCoffinInGraveB   = BurialType::coffinInGrave();

        $this->assertFalse($burialTypeCoffinInGraveA->isEqual($burialTypeUrnInGround));
        $this->assertFalse($burialTypeCoffinInGraveA->isEqual($burialTypeUrnInColumbarium));
        $this->assertFalse($burialTypeCoffinInGraveA->isEqual($burialTypeAshesUnderTree));
        $this->assertTrue($burialTypeCoffinInGraveA->isEqual($burialTypeCoffinInGraveB));
        $this->assertFalse($burialTypeUrnInGround->isEqual($burialTypeUrnInColumbarium));
        $this->assertFalse($burialTypeUrnInGround->isEqual($burialTypeAshesUnderTree));
        $this->assertFalse($burialTypeUrnInColumbarium->isEqual($burialTypeAshesUnderTree));
    }
}
