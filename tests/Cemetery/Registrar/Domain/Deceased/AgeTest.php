<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Deceased;

use Cemetery\Registrar\Domain\Deceased\Age;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AgeTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $age = new Age(82);
        $this->assertSame(82, $age->value());

        $age = new Age(0);
        $this->assertSame(0, $age->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Возраст не может иметь отрицательное значение.');
        new Age(-1);
    }

    public function testItStringifyable(): void
    {
        $age = new Age(82);

        $this->assertSame('82', (string) $age);
    }

    public function testItComparable(): void
    {
        $ageA = new Age(82);
        $ageB = new Age(15);
        $ageC = new Age(82);

        $this->assertFalse($ageA->isEqual($ageB));
        $this->assertTrue($ageA->isEqual($ageC));
        $this->assertFalse($ageB->isEqual($ageC));
    }
}
