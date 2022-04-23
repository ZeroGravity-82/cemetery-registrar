<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AccuracyTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $accuracy = new Accuracy('0.89');
        $this->assertSame('0.89', $accuracy->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Погрешность не может иметь отрицательное значение.');
        new Accuracy('-1.2');
    }

    public function testItFailsWithInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Погрешность "1.7A" имеет неверный формат.');
        new Accuracy('1.7A');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new Accuracy('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new Accuracy('   ');
    }

    public function testItStringifyable(): void
    {
        $accuracy = new Accuracy('0.89');
        $this->assertSame('0.89', (string) $accuracy);
    }

    public function testItComparable(): void
    {
        $accuracyA = new Accuracy('0.89');
        $accuracyB = new Accuracy('1.9');
        $accuracyC = new Accuracy('0.89');

        $this->assertFalse($accuracyA->isEqual($accuracyB));
        $this->assertTrue($accuracyA->isEqual($accuracyC));
        $this->assertFalse($accuracyB->isEqual($accuracyC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Погрешность не может иметь пустое значение.');
    }
}