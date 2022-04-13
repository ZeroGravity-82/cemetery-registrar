<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\BurialContainer;

use Cemetery\Registrar\Domain\BurialContainer\CoffinSize;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinSizeTest extends TestCase
{
    private const COFFIN_MIN_SIZE = 165;
    private const COFFIN_MAX_SIZE = 225;

    public function testItSuccessfullyCreated(): void
    {
        $coffin = new CoffinSize(self::COFFIN_MIN_SIZE);
        $this->assertSame(self::COFFIN_MIN_SIZE, $coffin->getValue());

        $coffin = new CoffinSize(self::COFFIN_MAX_SIZE);
        $this->assertSame(self::COFFIN_MAX_SIZE, $coffin->getValue());

        $coffinAvgSize = (int) ((self::COFFIN_MIN_SIZE + self::COFFIN_MAX_SIZE) / 2);
        $coffin        = new CoffinSize($coffinAvgSize);
        $this->assertSame($coffinAvgSize, $coffin->getValue());
    }

    public function testItFailsWithValueBelowAllowedRange(): void
    {
        $valueBelowAllowedRange = self::COFFIN_MIN_SIZE - 1;
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Размер гроба %d см находится вне допустимого диапазона [%d, %d] см.',
            $valueBelowAllowedRange,
            self::COFFIN_MIN_SIZE,
            self::COFFIN_MAX_SIZE
        ));
        new CoffinSize($valueBelowAllowedRange);
    }

    public function testItFailsWithValueAboveAllowedRange(): void
    {
        $valueAboveAllowedRange = self::COFFIN_MAX_SIZE + 1;
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(\sprintf(
            'Размер гроба %d см находится вне допустимого диапазона [%d, %d] см.',
            $valueAboveAllowedRange,
            self::COFFIN_MIN_SIZE,
            self::COFFIN_MAX_SIZE
        ));
        new CoffinSize($valueAboveAllowedRange);
    }
}
