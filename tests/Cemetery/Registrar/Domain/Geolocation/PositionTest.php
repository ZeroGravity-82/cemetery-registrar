<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Geolocation;

use Cemetery\Registrar\Domain\Geolocation\Accuracy;
use Cemetery\Registrar\Domain\Geolocation\Coordinates;
use Cemetery\Registrar\Domain\Geolocation\Position;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PositionTest extends TestCase
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
        $position = new Position($this->coordinatesA, $this->accuracyA);
        $this->assertSame($position->getCoordinates()->getLatitude(), '54.950357');
        $this->assertSame($position->getCoordinates()->getLongitude(), '82.7972252');
        $this->assertSame($position->getAccuracy()->getValue(), '1.2');

        $position = new Position($this->coordinatesA);
        $this->assertSame($position->getCoordinates()->getLatitude(), '54.950357');
        $this->assertSame($position->getCoordinates()->getLongitude(), '82.7972252');
        $this->assertNull($position->getAccuracy());
    }

    public function testItStringifyable(): void
    {
        $position = new Position($this->coordinatesA, $this->accuracyA);
        $this->assertSame('54.950357, 82.7972252 [&plusmn; 1.2m]', (string) $position);
    }

    public function testItComparable(): void
    {
        $positionA  = new Position($this->coordinatesA, $this->accuracyA);
        $positionB1 = new Position($this->coordinatesA, $this->accuracyB);
        $positionB2 = new Position($this->coordinatesB);
        $positionB3 = new Position($this->coordinatesC, $this->accuracyA);
        $positionC  = new Position($this->coordinatesA, $this->accuracyA);

        $this->assertFalse($positionA->isEqual($positionB1));
        $this->assertFalse($positionA->isEqual($positionB2));
        $this->assertFalse($positionA->isEqual($positionB3));
        $this->assertTrue($positionA->isEqual($positionC));
        $this->assertFalse($positionB1->isEqual($positionC));
        $this->assertFalse($positionB2->isEqual($positionC));
        $this->assertFalse($positionB3->isEqual($positionC));
    }
}
