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
        $coordinates = new Coordinates('54.950357', '82.7972252', '0.89');
        $this->assertSame($coordinates->getLatitude(), '54.950357');
        $this->assertSame($coordinates->getLongitude(), '82.7972252');
        $this->assertSame($coordinates->getAccuracy(), '0.89');

        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame($coordinates->getLatitude(), '54.950357');
        $this->assertSame($coordinates->getLongitude(), '82.7972252');
        $this->assertNull($coordinates->getAccuracy());

        $coordinates = new Coordinates('+54.950357', '+82.7972252', '0.89');
        $this->assertSame($coordinates->getLatitude(), '54.950357');
        $this->assertSame($coordinates->getLongitude(), '82.7972252');
        $this->assertSame($coordinates->getAccuracy(), '0.89');

        $coordinates = new Coordinates('-54.950357', '-82.7972252');
        $this->assertSame($coordinates->getLatitude(), '-54.950357');
        $this->assertSame($coordinates->getLongitude(), '-82.7972252');
        $this->assertNull($coordinates->getAccuracy());
    }

    public function testItFailsWithInvalidLatitudeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid latitude value "91.5".');
        new Coordinates('91.5', '82.7972252');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid latitude value "-91.5".');
        new Coordinates('-91.5', '82.7972252');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid latitude value "54.9A".');
        new Coordinates('54.9A', '82.7972252');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid latitude value "54".');
        new Coordinates('54', '82.7972252');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid latitude value ".950357".');
        new Coordinates('.950357', '82.7972252');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid latitude value "".');
        new Coordinates('', '82.7972252');
    }

    public function testItFailsWithInvalidLongitudeValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid longitude value "182.7".');
        new Coordinates('54.950357', '182.7');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid longitude value "-182.7".');
        new Coordinates('54.950357', '-182.7');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid longitude value "82.7B".');
        new Coordinates('54.950357', '82.7B');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid longitude value "82".');
        new Coordinates('54.950357', '82');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid longitude value ".734".');
        new Coordinates('54.950357', '.734');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid longitude value "".');
        new Coordinates('54.950357', '');
    }

    public function testItFailsWithInvalidAccuracyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid accuracy value "0".');
        new Coordinates('54.950357', '82.7972252', '0');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid accuracy value "-1.7".');
        new Coordinates('54.950357', '82.7972252', '-1.7');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid accuracy value "".');
        new Coordinates('54.950357', '82.7972252', '');
    }

    public function testItStringifyable(): void
    {
        $coordinates = new Coordinates('54.950357', '82.7972252', '0.89');
        $this->assertSame('54.950357, 82.7972252', (string) $coordinates);

        $coordinates = new Coordinates('54.950357', '82.7972252');
        $this->assertSame('54.950357, 82.7972252', (string) $coordinates);
    }

    public function testItComparable(): void
    {
        $coordinatesA  = new Coordinates('54.950357', '82.7972252', '0.89');
        $coordinatesB  = new Coordinates('54.950357', '82.7972252', '0.89');
        $coordinatesC1 = new Coordinates('44.950357', '82.7972252', '0.89');
        $coordinatesC2 = new Coordinates('54.950357', '72.7972252', '0.89');
        $coordinatesC3 = new Coordinates('54.950357', '72.7972252', '1.2');
        $coordinatesC4 = new Coordinates('54.950357', '82.7972252');

        $this->assertTrue($coordinatesA->isEqual($coordinatesB));
        $this->assertFalse($coordinatesA->isEqual($coordinatesC1));
        $this->assertFalse($coordinatesA->isEqual($coordinatesC2));
        $this->assertFalse($coordinatesA->isEqual($coordinatesC3));
        $this->assertFalse($coordinatesA->isEqual($coordinatesC4));
    }
}
