<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\GeoPosition;

use Cemetery\Registrar\Domain\GeoPosition\Accuracy;
use Cemetery\Registrar\Domain\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\GeoPosition\GeoPosition;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GeoPositionTest extends TestCase
{
    private Coordinates $coordinatesA;

    private Coordinates $coordinatesB;

    private Coordinates $coordinatesC;

    private Accuracy $accuracyA;

    private Accuracy $accuracyB;

    public function setUp(): void
    {
        $this->coordinatesA = new Coordinates('54.950357', '82.7972252');
        $this->coordinatesB = new Coordinates('44.950357', '82.7972252');
        $this->coordinatesC = new Coordinates('54.950357', '72.7972252');
        $this->accuracyA    = new Accuracy('1.2');
        $this->accuracyB    = new Accuracy('0.2');
    }

    public function testItSuccessfullyCreated(): void
    {
        $geoPosition = new GeoPosition($this->coordinatesA, $this->accuracyA);
        $this->assertSame('54.950357', $geoPosition->coordinates()->latitude());
        $this->assertSame('82.7972252', $geoPosition->coordinates()->longitude());
        $this->assertSame('1.2', $geoPosition->accuracy()->value());

        $geoPosition = new GeoPosition($this->coordinatesA, null);
        $this->assertSame('54.950357', $geoPosition->coordinates()->latitude());
        $this->assertSame('82.7972252', $geoPosition->coordinates()->longitude());
        $this->assertNull($geoPosition->accuracy());
    }

    public function testItStringifyable(): void
    {
        $geoPosition = new GeoPosition($this->coordinatesA, $this->accuracyA);
        $this->assertSame('54.950357, 82.7972252 [&plusmn; 1.2m]', (string) $geoPosition);
    }

    public function testItComparable(): void
    {
        $geoPositionA = new GeoPosition($this->coordinatesA, $this->accuracyA);
        $geoPositionB = new GeoPosition($this->coordinatesA, $this->accuracyB);
        $geoPositionC = new GeoPosition($this->coordinatesB, null);
        $geoPositionD = new GeoPosition($this->coordinatesC, $this->accuracyA);
        $geoPositionE = new GeoPosition($this->coordinatesA, $this->accuracyA);

        $this->assertFalse($geoPositionA->isEqual($geoPositionB));
        $this->assertFalse($geoPositionA->isEqual($geoPositionC));
        $this->assertFalse($geoPositionA->isEqual($geoPositionD));
        $this->assertTrue($geoPositionA->isEqual($geoPositionE));
        $this->assertFalse($geoPositionB->isEqual($geoPositionE));
        $this->assertFalse($geoPositionC->isEqual($geoPositionE));
        $this->assertFalse($geoPositionD->isEqual($geoPositionE));
    }
}
