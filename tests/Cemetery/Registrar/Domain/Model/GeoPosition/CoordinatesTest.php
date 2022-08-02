<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\GeoPosition;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoordinatesTest extends TestCase
{
    public function testItHasValidValuePatternConstant(): void
    {
        $this->assertSame('~^[+|\-]?\d+(?:\.\d+)?$~', Coordinates::VALUE_PATTERN);
    }

    public function testItSuccessfullyCreated(): void
    {
        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame('54.950357', $coordinates->latitude());
        $this->assertSame('82.7972252', $coordinates->longitude());

        $coordinates = new Coordinates('+54.9503570', '+082.7972252');
        $this->assertSame('54.950357', $coordinates->latitude());
        $this->assertSame('82.7972252', $coordinates->longitude());

        $coordinates = new Coordinates('-54.950357', '-82.7972252');
        $this->assertSame('-54.950357', $coordinates->latitude());
        $this->assertSame('-82.7972252', $coordinates->longitude());

        $coordinates = new Coordinates('000', '082');
        $this->assertSame('0.0', $coordinates->latitude());
        $this->assertSame('82.0', $coordinates->longitude());

        $coordinates = new Coordinates('-054', '0');
        $this->assertSame('-54.0', $coordinates->latitude());
        $this->assertSame('0.0', $coordinates->longitude());
    }

    public function testItFailsWithLatitudeValueOutOfRange(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Широта "91.5" находится вне допустимого диапазона [-90, 90].');
        new Coordinates('91.5', '82.7972252');
    }

    public function testItFailsWithLatitudeValueInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Неверный формат широты.');
        new Coordinates('54.9A', '82.7972252');
    }

    public function testItFailsWithEmptyLatitudeValue(): void
    {
        $this->expectExceptionForEmptyValue('Широта');
        new Coordinates('', '82.7972252');
    }

    public function testItFailsWithLatitudeValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Широта');
        new Coordinates('   ', '82.7972252');
    }

    public function testItFailsLongitudeValueOutOfRange(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Долгота "182.7" находится вне допустимого диапазона [-180, 180].');
        new Coordinates('54.950357', '182.7');
    }

    public function testItFailsWithLongitudeValueInvalidFormat(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Неверный формат долготы.');
        new Coordinates('54.950357', '-82.7A');
    }

    public function testItFailsWithEmptyLongitudeValue(): void
    {
        $this->expectExceptionForEmptyValue('Долгота');
        new Coordinates('54.950357', '');
    }

    public function testItFailsWithLongitudeValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Долгота');
        new Coordinates('54.950357', '   ');
    }

    public function testItStringifyable(): void
    {
        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame('54.950357, 82.7972252', (string) $coordinates);

        $coordinates = new Coordinates('-054.9503570', '+82.7972252');
        $this->assertSame('-54.950357, 82.7972252', (string) $coordinates);
    }

    public function testItComparable(): void
    {
        $coordinatesA = new Coordinates('-54.950357', '82.7972252');
        $coordinatesB = new Coordinates('44.950357', '82.7972252');
        $coordinatesC = new Coordinates('54.950357', '72.7972252');
        $coordinatesD = new Coordinates('-054.950357', '+82.79722520');

        $this->assertFalse($coordinatesA->isEqual($coordinatesB));
        $this->assertFalse($coordinatesA->isEqual($coordinatesC));
        $this->assertTrue($coordinatesA->isEqual($coordinatesD));
        $this->assertFalse($coordinatesB->isEqual($coordinatesD));
        $this->assertFalse($coordinatesC->isEqual($coordinatesD));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(\sprintf('%s не может иметь пустое значение.', $name));
    }
}
