<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\GeoPosition;

use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class ErrorTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $error = new Error('0.25');
        $this->assertSame('0.25', $error->value());

        $error = new Error('0');
        $this->assertSame('0.0', $error->value());

        $error = new Error('012.50');
        $this->assertSame('12.5', $error->value());

        $error = new Error('1');
        $this->assertSame('1.0', $error->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Погрешность не может иметь отрицательное значение.');
        new Error('-1.2');
    }

    public function testItFailsWithInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Погрешность "1.7A" имеет неверный формат.');
        new Error('1.7A');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Error('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Error('   ');
    }

    public function testItStringifyable(): void
    {
        $error = new Error('0.89');
        $this->assertSame('0.89', (string) $error);
    }

    public function testItComparable(): void
    {
        $errorA = new Error('0.89');
        $errorB = new Error('1.9');
        $errorC = new Error('00.890');

        $this->assertFalse($errorA->isEqual($errorB));
        $this->assertTrue($errorA->isEqual($errorC));
        $this->assertFalse($errorB->isEqual($errorC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Погрешность не может иметь пустое значение.');
    }
}
