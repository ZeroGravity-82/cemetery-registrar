<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\NaturalPerson\PlaceOfBirth;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PlaceOfBirthTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $address = new PlaceOfBirth('г. Новосибирск');
        $this->assertSame('г. Новосибирск', $address->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new PlaceOfBirth('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new PlaceOfBirth('   ');
    }

    public function testItStringifyable(): void
    {
        $placeOfBirth = new PlaceOfBirth('г. Новосибирск');
        $this->assertSame('г. Новосибирск', (string) $placeOfBirth);
    }

    public function testItComparable(): void
    {
        $placeOfBirthA = new PlaceOfBirth('г. Новосибирск');
        $placeOfBirthB = new PlaceOfBirth('г. Москва');
        $placeOfBirthC = new PlaceOfBirth('г. Новосибирск');

        $this->assertFalse($placeOfBirthA->isEqual($placeOfBirthB));
        $this->assertTrue($placeOfBirthA->isEqual($placeOfBirthC));
        $this->assertFalse($placeOfBirthB->isEqual($placeOfBirthC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Место рождения не может иметь пустое значение.');
    }
}
