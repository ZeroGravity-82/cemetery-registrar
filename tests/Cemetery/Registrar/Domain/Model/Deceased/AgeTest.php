<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Deceased;

use Cemetery\Registrar\Domain\Model\Deceased\Age;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class AgeTest extends TestCase
{
    private const MAX_AGE = 125;

    public function testItSuccessfullyCreated(): void
    {
        $age = new Age(0);
        $this->assertSame(0, $age->value());

        $age = new Age(self::MAX_AGE);
        $this->assertSame(self::MAX_AGE, $age->value());

        $avgAge = (int) (self::MAX_AGE / 2);
        $age    = new Age($avgAge);
        $this->assertSame($avgAge, $age->value());
    }

    public function testItSuccessfullyCreatedFromDates(): void
    {
        $bornAt     = new \DateTimeImmutable('1968-10-24');
        $targetDate = new \DateTimeImmutable('2021-01-05');
        $age = Age::fromDates($bornAt, $targetDate);
        $this->assertSame(52, $age->value());

        $bornAt     = new \DateTimeImmutable('1947-01-19');
        $targetDate = new \DateTimeImmutable('2016-01-20');
        $age = Age::fromDates($bornAt, $targetDate);
        $this->assertSame(69, $age->value());

        $bornAt     = new \DateTimeImmutable('2021-02-13');
        $targetDate = new \DateTimeImmutable('2021-09-15');
        $age = Age::fromDates($bornAt, $targetDate);
        $this->assertSame(0, $age->value());
    }

    public function testItFailsWithNegativeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Возраст не может иметь отрицательное значение.');
        new Age(-1);
    }

    public function testItFailsWithTooMuchValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Возраст не может превышать %d лет.', self::MAX_AGE));
        new Age(self::MAX_AGE + 1);
    }

    public function testItFailsWithMistakenlySwappedDates(): void
    {
        $bornAt     = new \DateTimeImmutable('2021-01-05');
        $targetDate = new \DateTimeImmutable('1968-10-24');
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Конечная дата не может предшествовать дате рождения.');
        Age::fromDates($bornAt, $targetDate);
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
