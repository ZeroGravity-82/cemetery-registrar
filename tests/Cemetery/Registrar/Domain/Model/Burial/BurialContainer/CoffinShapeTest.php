<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinShape;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeTest extends TestCase
{
    private const TRAPEZOID_VALUE             = 'TRAPEZOID';
    private const TRAPEZOID_LABEL             = 'трапеция';
    private const GREEK_WITH_HANDLES_VALUE    = 'GREEK_WITH_HANDLES';
    private const GREEK_WITH_HANDLES_LABEL    = 'грек (с ручками)';
    private const GREEK_WITHOUT_HANDLES_VALUE = 'GREEK_WITHOUT_HANDLES';
    private const GREEK_WITHOUT_HANDLES_LABEL = 'грек (без ручек)';
    private const AMERICAN_VALUE              = 'AMERICAN';
    private const AMERICAN_LABEL              = 'американец';

    public function testItSuccessfullyCreated(): void
    {
        $coffinShape = new CoffinShape(CoffinShape::TRAPEZOID);
        $this->assertSame(self::TRAPEZOID_VALUE, $coffinShape->value());
        $this->assertSame(self::TRAPEZOID_LABEL, $coffinShape->label());
        $coffinShape = CoffinShape::trapezoid();
        $this->assertSame(self::TRAPEZOID_VALUE, $coffinShape->value());
        $this->assertSame(self::TRAPEZOID_LABEL, $coffinShape->label());

        $coffinShape = new CoffinShape(CoffinShape::GREEK_WITH_HANDLES);
        $this->assertSame(self::GREEK_WITH_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITH_HANDLES_LABEL, $coffinShape->label());
        $coffinShape = CoffinShape::greekWithHandles();
        $this->assertSame(self::GREEK_WITH_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITH_HANDLES_LABEL, $coffinShape->label());

        $coffinShape = new CoffinShape(CoffinShape::GREEK_WITHOUT_HANDLES);
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_LABEL, $coffinShape->label());
        $coffinShape = CoffinShape::greekWithoutHandles();
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_LABEL, $coffinShape->label());

        $coffinShape = new CoffinShape(CoffinShape::AMERICAN);
        $this->assertSame(self::AMERICAN_VALUE, $coffinShape->value());
        $this->assertSame(self::AMERICAN_LABEL, $coffinShape->label());
    }

    public function testItFailsWithNotSupportedValue(): void
    {
        $unsupportedShape = 'UNSUPPORTED_SHAPE';
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Неподдерживаемая форма гроба "%s", должна быть одна из "%s", "%s", "%s", "%s".',
            $unsupportedShape,
            self::TRAPEZOID_VALUE,
            self::GREEK_WITH_HANDLES_VALUE,
            self::GREEK_WITHOUT_HANDLES_VALUE,
            self::AMERICAN_VALUE
        ));
        new CoffinShape($unsupportedShape);
    }

    public function testItStringifyable(): void
    {
        $coffinShape = CoffinShape::trapezoid();
        $this->assertSame(self::TRAPEZOID_LABEL, (string) $coffinShape);

        $coffinShape = CoffinShape::greekWithHandles();
        $this->assertSame(self::GREEK_WITH_HANDLES_LABEL, (string) $coffinShape);

        $coffinShape = CoffinShape::greekWithoutHandles();
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_LABEL, (string) $coffinShape);

        $coffinShape = CoffinShape::american();
        $this->assertSame(self::AMERICAN_LABEL, (string) $coffinShape);
    }

    public function testItComparable(): void
    {
        $coffinShapeA = CoffinShape::trapezoid();
        $coffinShapeB = CoffinShape::greekWithHandles();
        $coffinShapeC = CoffinShape::greekWithoutHandles();
        $coffinShapeD = CoffinShape::american();
        $coffinShapeE = CoffinShape::trapezoid();
        $this->assertFalse($coffinShapeA->isEqual($coffinShapeB));
        $this->assertFalse($coffinShapeA->isEqual($coffinShapeC));
        $this->assertFalse($coffinShapeA->isEqual($coffinShapeD));
        $this->assertTrue($coffinShapeA->isEqual($coffinShapeE));
        $this->assertFalse($coffinShapeB->isEqual($coffinShapeC));
        $this->assertFalse($coffinShapeB->isEqual($coffinShapeD));
        $this->assertFalse($coffinShapeB->isEqual($coffinShapeE));
        $this->assertFalse($coffinShapeC->isEqual($coffinShapeD));
        $this->assertFalse($coffinShapeC->isEqual($coffinShapeE));
        $this->assertFalse($coffinShapeD->isEqual($coffinShapeE));
    }
}
