<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Burial\BurialContainer;

use Cemetery\Registrar\Domain\Model\Burial\BurialContainer\CoffinSize;
use Cemetery\Registrar\Domain\Model\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoffinSizeTest extends TestCase
{
    private const MIN_SIZE = 165;
    private const MAX_SIZE = 225;

    public function testItSuccessfullyCreated(): void
    {
        $coffinSize = new CoffinSize(self::MIN_SIZE);
        $this->assertSame(self::MIN_SIZE, $coffinSize->value());

        $coffinSize = new CoffinSize(self::MAX_SIZE);
        $this->assertSame(self::MAX_SIZE, $coffinSize->value());

        $coffinAvgSize = (int) ((self::MIN_SIZE + self::MAX_SIZE) / 2);
        $coffinSize    = new CoffinSize($coffinAvgSize);
        $this->assertSame($coffinAvgSize, $coffinSize->value());
    }

    public function testItFailsWithValueBelowAllowedRange(): void
    {
        $coffinSizeBelowAllowedRange = self::MIN_SIZE - 1;
        $this->expectExceptionForValueOutOfRange($coffinSizeBelowAllowedRange);
        new CoffinSize($coffinSizeBelowAllowedRange);
    }

    public function testItFailsWithValueAboveAllowedRange(): void
    {
        $coffinSizeAboveAllowedRange = self::MAX_SIZE + 1;
        $this->expectExceptionForValueOutOfRange($coffinSizeAboveAllowedRange);
        new CoffinSize($coffinSizeAboveAllowedRange);
    }

    public function testItStringifyable(): void
    {
        $coffinSize = new CoffinSize(180);
        $this->assertSame('180', (string) $coffinSize);
    }

    public function testItComparable(): void
    {
        $coffinSizeA = new CoffinSize(180);
        $coffinSizeB = new CoffinSize(175);
        $coffinSizeC = new CoffinSize(180);
        $this->assertFalse($coffinSizeA->isEqual($coffinSizeB));
        $this->assertTrue($coffinSizeA->isEqual($coffinSizeC));
        $this->assertFalse($coffinSizeB->isEqual($coffinSizeC));
    }

    private function expectExceptionForValueOutOfRange(int $value): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf(
            'Размер гроба %d см находится вне допустимого диапазона [%d, %d] см.',
            $value,
            self::MIN_SIZE,
            self::MAX_SIZE
        ));
    }
}
