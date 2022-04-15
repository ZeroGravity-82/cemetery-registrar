<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\CoffinShape;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinShapeTest extends TestCase
{
    private const TRAPEZOID_VALUE                    = 'trapezoid';
    private const TRAPEZOID_DISPLAY_NAME             = 'трапеция';
    private const GREEK_WITH_HANDLES_VALUE           = 'greek_with_handles';
    private const GREEK_WITH_HANDLES_DISPLAY_NAME    = 'грек (с ручками)';
    private const GREEK_WITHOUT_HANDLES_VALUE        = 'greek_without_handles';
    private const GREEK_WITHOUT_HANDLES_DISPLAY_NAME = 'грек (без ручек)';
    private const AMERICAN_VALUE                     = 'american';
    private const AMERICAN_DISPLAY_NAME              = 'американец';

    public function testItSuccessfullyCreated(): void
    {
        $coffinShape = new CoffinShape(CoffinShape::TRAPEZOID);
        $this->assertSame(self::TRAPEZOID_VALUE, $coffinShape->value());
        $this->assertSame(self::TRAPEZOID_DISPLAY_NAME, $coffinShape->displayName());
        $coffinShape = CoffinShape::trapezoid();
        $this->assertSame(self::TRAPEZOID_VALUE, $coffinShape->value());
        $this->assertSame(self::TRAPEZOID_DISPLAY_NAME, $coffinShape->displayName());

        $coffinShape = new CoffinShape(CoffinShape::GREEK_WITH_HANDLES);
        $this->assertSame(self::GREEK_WITH_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITH_HANDLES_DISPLAY_NAME, $coffinShape->displayName());
        $coffinShape = CoffinShape::greekWithHandles();
        $this->assertSame(self::GREEK_WITH_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITH_HANDLES_DISPLAY_NAME, $coffinShape->displayName());

        $coffinShape = new CoffinShape(CoffinShape::GREEK_WITHOUT_HANDLES);
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_DISPLAY_NAME, $coffinShape->displayName());
        $coffinShape = CoffinShape::greekWithoutHandles();
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_VALUE, $coffinShape->value());
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_DISPLAY_NAME, $coffinShape->displayName());

        $coffinShape = new CoffinShape(CoffinShape::AMERICAN);
        $this->assertSame(self::AMERICAN_VALUE, $coffinShape->value());
        $this->assertSame(self::AMERICAN_DISPLAY_NAME, $coffinShape->displayName());
    }

    public function testItFailsWithNotSupportedValue(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Неподдерживаемая форма гроба "unsupported_shape", должна быть одна из "%s", "%s", "%s", "%s".',
            self::TRAPEZOID_VALUE,
            self::GREEK_WITH_HANDLES_VALUE,
            self::GREEK_WITHOUT_HANDLES_VALUE,
            self::AMERICAN_VALUE
        ));
        new CoffinShape('unsupported_shape');
    }

    public function testItStringifyable(): void
    {
        $coffinShape = CoffinShape::trapezoid();
        $this->assertSame(self::TRAPEZOID_DISPLAY_NAME, (string) $coffinShape);

        $coffinShape = CoffinShape::greekWithHandles();
        $this->assertSame(self::GREEK_WITH_HANDLES_DISPLAY_NAME, (string) $coffinShape);

        $coffinShape = CoffinShape::greekWithoutHandles();
        $this->assertSame(self::GREEK_WITHOUT_HANDLES_DISPLAY_NAME, (string) $coffinShape);

        $coffinShape = CoffinShape::american();
        $this->assertSame(self::AMERICAN_DISPLAY_NAME, (string) $coffinShape);
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
