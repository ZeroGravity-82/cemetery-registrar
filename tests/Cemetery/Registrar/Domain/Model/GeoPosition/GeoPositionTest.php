<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\GeoPosition;

use Cemetery\Registrar\Domain\Model\GeoPosition\Coordinates;
use Cemetery\Registrar\Domain\Model\GeoPosition\Error;
use Cemetery\Registrar\Domain\Model\GeoPosition\GeoPosition;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class GeoPositionTest extends TestCase
{
    private Coordinates $coordinatesA;

    private Coordinates $coordinatesB;

    private Coordinates $coordinatesC;

    private Error $errorA;

    private Error $errorB;

    public function setUp(): void
    {
        $this->coordinatesA = new Coordinates('54.950357', '82.7972252');
        $this->coordinatesB = new Coordinates('44.950357', '82.7972252');
        $this->coordinatesC = new Coordinates('54.950357', '72.7972252');
        $this->errorA       = new Error('1.2');
        $this->errorB       = new Error('0.2');
    }

    public function testItSuccessfullyCreated(): void
    {
        $geoPosition = new GeoPosition($this->coordinatesA, $this->errorA);
        $this->assertSame('54.950357', $geoPosition->coordinates()->latitude());
        $this->assertSame('82.7972252', $geoPosition->coordinates()->longitude());
        $this->assertSame('1.2', $geoPosition->error()->value());

        $geoPosition = new GeoPosition($this->coordinatesA, null);
        $this->assertSame('54.950357', $geoPosition->coordinates()->latitude());
        $this->assertSame('82.7972252', $geoPosition->coordinates()->longitude());
        $this->assertNull($geoPosition->error());
    }

    public function testItStringifyable(): void
    {
        $geoPosition = new GeoPosition($this->coordinatesA, $this->errorA);
        $this->assertSame('54.950357, 82.7972252 [&plusmn; 1.2m]', (string) $geoPosition);
    }

    public function testItComparable(): void
    {
        $geoPositionA = new GeoPosition($this->coordinatesA, $this->errorA);
        $geoPositionB = new GeoPosition($this->coordinatesA, $this->errorB);
        $geoPositionC = new GeoPosition($this->coordinatesB, null);
        $geoPositionD = new GeoPosition($this->coordinatesC, $this->errorA);
        $geoPositionE = new GeoPosition($this->coordinatesA, $this->errorA);

        $this->assertFalse($geoPositionA->isEqual($geoPositionB));
        $this->assertFalse($geoPositionA->isEqual($geoPositionC));
        $this->assertFalse($geoPositionA->isEqual($geoPositionD));
        $this->assertTrue($geoPositionA->isEqual($geoPositionE));
        $this->assertFalse($geoPositionB->isEqual($geoPositionE));
        $this->assertFalse($geoPositionC->isEqual($geoPositionE));
        $this->assertFalse($geoPositionD->isEqual($geoPositionE));
    }
}
