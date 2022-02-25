<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Geolocation;

use Cemetery\Registrar\Domain\Geolocation\Accuracy;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AccuracyTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $accuracy = new Accuracy('0.89');
        $this->assertSame('0.89', $accuracy->getValue());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Accuracy value cannot be negative.');
        new Accuracy('-1.2');
    }

    public function testItFailsWithInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Accuracy value "1.7A" has an invalid format.');
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
        $coordinates = new Accuracy('0.89');
        $this->assertSame('0.89', (string) $coordinates);
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
