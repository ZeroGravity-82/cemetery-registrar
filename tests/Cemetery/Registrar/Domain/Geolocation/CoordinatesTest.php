<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Geolocation;

use Cemetery\Registrar\Domain\Geolocation\Coordinates;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CoordinatesTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame('54.950357', $coordinates->getLatitude());
        $this->assertSame('82.7972252', $coordinates->getLongitude());

        $coordinates = new Coordinates('+54.950357', '+82.7972252');
        $this->assertSame('54.950357', $coordinates->getLatitude());
        $this->assertSame('82.7972252', $coordinates->getLongitude());

        $coordinates = new Coordinates('-54.950357', '-82.7972252');
        $this->assertSame('-54.950357', $coordinates->getLatitude());
        $this->assertSame('-82.7972252', $coordinates->getLongitude());
    }

    public function testItFailsWithLatitudeValueOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Latitude value "91.5" is out of valid range [-90, 90].');
        new Coordinates('91.5', '82.7972252');
    }

    public function testItFailsWithLatitudeValueInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Latitude value "54.9A" has an invalid format.');
        new Coordinates('54.9A', '82.7972252');
    }

    public function testItFailsWithEmptyLatitudeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Latitude value cannot be empty.');
        new Coordinates('', '82.7972252');
    }

    public function testItFailsLongitudeValueOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Longitude value "182.7" is out of valid range [-180, 180].');
        new Coordinates('54.950357', '182.7');
    }

    public function testItFailsWithLongitudeValueInvalidFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Longitude value "-82.7A" has an invalid format.');
        new Coordinates('54.950357', '-82.7A');
    }

    public function testItFailsWithEmptyLongitudeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Longitude value cannot be empty.');
        new Coordinates('54.950357', '');
    }

    public function testItStringifyable(): void
    {
        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame('54.950357, 82.7972252', (string) $coordinates);

        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame('54.950357, 82.7972252', (string) $coordinates);
    }

    public function testItComparable(): void
    {
        $coordinatesA = new Coordinates('54.950357', '82.7972252');
        $coordinatesB = new Coordinates('44.950357', '82.7972252');
        $coordinatesC = new Coordinates('54.950357', '72.7972252');
        $coordinatesD = new Coordinates('54.950357', '82.7972252');

        $this->assertFalse($coordinatesA->isEqual($coordinatesB));
        $this->assertFalse($coordinatesA->isEqual($coordinatesC));
        $this->assertTrue($coordinatesA->isEqual($coordinatesD));
        $this->assertFalse($coordinatesB->isEqual($coordinatesD));
        $this->assertFalse($coordinatesC->isEqual($coordinatesD));
    }
}
