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
        $this->assertSame('54.950357', $position->coordinates()->latitude());
        $this->assertSame('82.7972252', $position->coordinates()->longitude());
        $this->assertSame('1.2', $position->accuracy()->value());

        $position = new Position($this->coordinatesA);
        $this->assertSame('54.950357', $position->coordinates()->latitude());
        $this->assertSame('82.7972252', $position->coordinates()->longitude());
        $this->assertNull($position->accuracy());
    }

    public function testItStringifyable(): void
    {
        $position = new Position($this->coordinatesA, $this->accuracyA);
        $this->assertSame('54.950357, 82.7972252 [&plusmn; 1.2m]', (string) $position);
    }

    public function testItComparable(): void
    {
        $positionA = new Position($this->coordinatesA, $this->accuracyA);
        $positionB = new Position($this->coordinatesA, $this->accuracyB);
        $positionC = new Position($this->coordinatesB);
        $positionD = new Position($this->coordinatesC, $this->accuracyA);
        $positionE = new Position($this->coordinatesA, $this->accuracyA);

        $this->assertFalse($positionA->isEqual($positionB));
        $this->assertFalse($positionA->isEqual($positionC));
        $this->assertFalse($positionA->isEqual($positionD));
        $this->assertTrue($positionA->isEqual($positionE));
        $this->assertFalse($positionB->isEqual($positionE));
        $this->assertFalse($positionC->isEqual($positionE));
        $this->assertFalse($positionD->isEqual($positionE));
    }
}
