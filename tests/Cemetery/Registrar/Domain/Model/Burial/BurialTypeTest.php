<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial;

use Cemetery\Registrar\Domain\Model\Burial\BurialType;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTypeTest extends TestCase
{
    private const COFFIN_IN_GRAVE_SITE_VALUE      = 'COFFIN_IN_GRAVE_SITE';
    private const COFFIN_IN_GRAVE_SITE_LABEL      = 'гробом в могилу';
    private const URN_IN_GRAVE_SITE_VALUE         = 'URN_IN_GRAVE_SITE';
    private const URN_IN_GRAVE_SITE_LABEL         = 'урной в могилу';
    private const URN_IN_COLUMBARIUM_NICHE_VALUE  = 'URN_IN_COLUMBARIUM_NICHE';
    private const URN_IN_COLUMBARIUM_NICHE_LABEL  = 'урной в колумбарную нишу';
    private const ASHES_UNDER_MEMORIAL_TREE_VALUE = 'ASHES_UNDER_MEMORIAL_TREE';
    private const ASHES_UNDER_MEMORIAL_TREE_LABEL = 'прахом под деревом';

    public function testItHasValidValueConstants(): void
    {
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_VALUE,      BurialType::COFFIN_IN_GRAVE_SITE);
        $this->assertSame(self::URN_IN_GRAVE_SITE_VALUE,         BurialType::URN_IN_GRAVE_SITE);
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_VALUE,  BurialType::URN_IN_COLUMBARIUM_NICHE);
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_VALUE, BurialType::ASHES_UNDER_MEMORIAL_TREE);
    }

    public function testItHasValidLabelsConstant(): void
    {
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_LABEL,      BurialType::LABELS[self::COFFIN_IN_GRAVE_SITE_VALUE]);
        $this->assertSame(self::URN_IN_GRAVE_SITE_LABEL,         BurialType::LABELS[self::URN_IN_GRAVE_SITE_VALUE]);
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_LABEL,  BurialType::LABELS[self::URN_IN_COLUMBARIUM_NICHE_VALUE]);
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_LABEL, BurialType::LABELS[self::ASHES_UNDER_MEMORIAL_TREE_VALUE]);
    }

    public function testItSuccessfullyCreated(): void
    {
        $burialType = new BurialType(BurialType::COFFIN_IN_GRAVE_SITE);
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_VALUE, $burialType->value());
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_LABEL, $burialType->label());
        $burialType = BurialType::coffinInGraveSite();
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_VALUE, $burialType->value());
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_LABEL, $burialType->label());

        $burialType = new BurialType(BurialType::URN_IN_GRAVE_SITE);
        $this->assertSame(self::URN_IN_GRAVE_SITE_VALUE, $burialType->value());
        $this->assertSame(self::URN_IN_GRAVE_SITE_LABEL, $burialType->label());
        $burialType = BurialType::urnInGraveSite();
        $this->assertSame(self::URN_IN_GRAVE_SITE_VALUE, $burialType->value());
        $this->assertSame(self::URN_IN_GRAVE_SITE_LABEL, $burialType->label());

        $burialType = new BurialType(BurialType::URN_IN_COLUMBARIUM_NICHE);
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_VALUE, $burialType->value());
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_LABEL, $burialType->label());
        $burialType = BurialType::urnInColumbariumNiche();
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_VALUE, $burialType->value());
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_LABEL, $burialType->label());

        $burialType = new BurialType(BurialType::ASHES_UNDER_MEMORIAL_TREE);
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_VALUE, $burialType->value());
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_LABEL, $burialType->label());
        $burialType = BurialType::ashesUnderMemorialTree();
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_VALUE, $burialType->value());
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_LABEL, $burialType->label());
    }

    public function testItFailsWithUnsupportedValue(): void
    {
        $unsupportedType = 'UNSUPPORTED_TYPE';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Неподдерживаемый тип захоронения "%s", должен быть один из "%s", "%s", "%s", "%s".',
            $unsupportedType,
            self::COFFIN_IN_GRAVE_SITE_VALUE,
            self::URN_IN_GRAVE_SITE_VALUE,
            self::URN_IN_COLUMBARIUM_NICHE_VALUE,
            self::ASHES_UNDER_MEMORIAL_TREE_VALUE,
        ));
        new BurialType($unsupportedType);
    }

    public function testItStringifyable(): void
    {
        $burialType = BurialType::coffinInGraveSite();
        $this->assertSame(self::COFFIN_IN_GRAVE_SITE_LABEL, (string) $burialType);

        $burialType = BurialType::urnInGraveSite();
        $this->assertSame(self::URN_IN_GRAVE_SITE_LABEL, (string) $burialType);

        $burialType = BurialType::urnInColumbariumNiche();
        $this->assertSame(self::URN_IN_COLUMBARIUM_NICHE_LABEL, (string) $burialType);

        $burialType = BurialType::ashesUnderMemorialTree();
        $this->assertSame(self::ASHES_UNDER_MEMORIAL_TREE_LABEL, (string) $burialType);
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
        $this->assertFalse($burialTypeB->isEqual($burialTypeE));
        $this->assertFalse($burialTypeC->isEqual($burialTypeD));
        $this->assertFalse($burialTypeC->isEqual($burialTypeE));
        $this->assertFalse($burialTypeD->isEqual($burialTypeE));
    }
}
