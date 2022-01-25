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
        $burialType = new BurialType(BurialType::COFFIN_IN_GROUND);
        $this->assertSame(BurialType::COFFIN_IN_GROUND, $burialType->getValue());
        $this->assertTrue($burialType->isCoffinInGround());
        $this->assertFalse($burialType->isUrnInGround());
        $this->assertFalse($burialType->isUrnInOpenColumbarium());
        $this->assertFalse($burialType->isUrnInClosedColumbarium());
        $this->assertFalse($burialType->isUrnInSarcophagus());
        $this->assertFalse($burialType->isAshesUnderTree());

        $burialType = BurialType::urnInOpenColumbarium();
        $this->assertSame(BurialType::URN_IN_OPEN_COLUMBARIUM, $burialType->getValue());
        $this->assertFalse($burialType->isCoffinInGround());
        $this->assertFalse($burialType->isUrnInGround());
        $this->assertTrue($burialType->isUrnInOpenColumbarium());
        $this->assertFalse($burialType->isUrnInClosedColumbarium());
        $this->assertFalse($burialType->isUrnInSarcophagus());
        $this->assertFalse($burialType->isAshesUnderTree());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Unsupported burial type "wrong_type", expected to be one of "%s", "%s", "%s", "%s", "%s", "%s".',
            BurialType::COFFIN_IN_GROUND,
            BurialType::URN_IN_GROUND,
            BurialType::URN_IN_OPEN_COLUMBARIUM,
            BurialType::URN_IN_CLOSED_COLUMBARIUM,
            BurialType::URN_IN_SARCOPHAGUS,
            BurialType::ASHES_UNDER_TREE,
        ));
        new BurialType('wrong_type');
    }

    public function testItStringifyable(): void
    {
        $burialType = BurialType::coffinInGround();

        $this->assertSame(BurialType::COFFIN_IN_GROUND, (string) $burialType);
    }

    public function testItComparable(): void
    {
        $burialTypeCoffinInGroundA        = BurialType::coffinInGround();
        $burialTypeUrnInGround            = BurialType::urnInGround();
        $burialTypeUrnInOpenColumbarium   = BurialType::urnInOpenColumbarium();
        $burialTypeUrnInClosedColumbarium = BurialType::urnInClosedColumbarium();
        $burialTypeUrnInSarcophagus       = BurialType::urnInSarcophagus();
        $burialTypeAshesUnderTree         = BurialType::ashesUnderTree();
        $burialTypeCoffinInGroundB        = BurialType::coffinInGround();

        $this->assertFalse($burialTypeCoffinInGroundA->isEqual($burialTypeUrnInGround));
        $this->assertFalse($burialTypeCoffinInGroundA->isEqual($burialTypeUrnInOpenColumbarium));
        $this->assertFalse($burialTypeCoffinInGroundA->isEqual($burialTypeUrnInClosedColumbarium));
        $this->assertFalse($burialTypeCoffinInGroundA->isEqual($burialTypeUrnInSarcophagus));
        $this->assertFalse($burialTypeCoffinInGroundA->isEqual($burialTypeAshesUnderTree));
        $this->assertTrue($burialTypeCoffinInGroundA->isEqual($burialTypeCoffinInGroundB));
        $this->assertFalse($burialTypeUrnInGround->isEqual($burialTypeUrnInOpenColumbarium));
        $this->assertFalse($burialTypeUrnInGround->isEqual($burialTypeUrnInClosedColumbarium));
        $this->assertFalse($burialTypeUrnInGround->isEqual($burialTypeUrnInSarcophagus));
        $this->assertFalse($burialTypeUrnInGround->isEqual($burialTypeAshesUnderTree));
        $this->assertFalse($burialTypeUrnInOpenColumbarium->isEqual($burialTypeUrnInClosedColumbarium));
        $this->assertFalse($burialTypeUrnInOpenColumbarium->isEqual($burialTypeUrnInSarcophagus));
        $this->assertFalse($burialTypeUrnInOpenColumbarium->isEqual($burialTypeAshesUnderTree));
        $this->assertFalse($burialTypeUrnInClosedColumbarium->isEqual($burialTypeUrnInSarcophagus));
        $this->assertFalse($burialTypeUrnInClosedColumbarium->isEqual($burialTypeAshesUnderTree));
        $this->assertFalse($burialTypeUrnInSarcophagus->isEqual($burialTypeAshesUnderTree));
    }
}
