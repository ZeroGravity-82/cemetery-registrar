<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonBuilder;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonFactoryTest extends TestCase
{
    private MockObject|NaturalPersonBuilder $mockNaturalPersonBuilder;
    private NaturalPersonFactory            $naturalPersonFactory;
    private MockObject|NaturalPerson        $mockNaturalPerson;

    public function setUp(): void
    {
        $this->mockNaturalPersonBuilder = $this->createMock(NaturalPersonBuilder::class);
        $this->naturalPersonFactory     = new NaturalPersonFactory($this->mockNaturalPersonBuilder);
        $this->mockNaturalPerson        = $this->createMock(NaturalPerson::class);
    }

    public function testItCreatesNaturalPersonForDeceased(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $bornAt   = new \DateTimeImmutable('1940-05-10');
        $this->mockNaturalPersonBuilder->expects($this->once())->method('initialize')->with($fullName);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addBornAt')->with($bornAt);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('build')->willReturn($this->mockNaturalPerson);
        $naturalPerson = $this->naturalPersonFactory->createForDeceased($fullName, $bornAt);
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
    }

    public function testItCreatesNaturalPersonForDeceasedWithoutOptionalFields(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $this->mockNaturalPersonBuilder->expects($this->once())->method('initialize')->with($fullName);
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addBornAt');
        $this->mockNaturalPersonBuilder->expects($this->once())->method('build')->willReturn($this->mockNaturalPerson);
        $naturalPerson = $this->naturalPersonFactory->createForDeceased($fullName, null);
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertNull($naturalPerson->bornAt());
    }

    public function testItFailsToCreateNaturalPersonForDeceasedWithoutFullName(): void
    {
        $this->expectExceptionForNotProvidedFullName();
        $this->naturalPersonFactory->createForDeceased(null, null);
    }

    public function testItCreatesNaturalPersonForCustomer(): void
    {
        $fullName             = 'Иванов Иван Иванович';
        $phone                = '+7-913-777-88-99';
        $phoneAdditional      = '8(383)123-45-67';
        $email                = 'info@google.com';
        $address              = 'г. Новосибирск, ул. 3 Интернационала, д. 127';
        $bornAt               = new \DateTimeImmutable('1940-05-10');
        $placeOfBirth         = 'г. Новосибирск';
        $passportSeries       = '1234';
        $passportNumber       = '567890';
        $passportIssuedAt     = new \DateTimeImmutable('2002-10-28');
        $passportIssuedBy     = 'УВД Кировского района города Новосибирска';
        $passportDivisionCode = '540-001';
        $this->mockNaturalPersonBuilder->expects($this->once())->method('initialize')->with($fullName);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addPhone')->with($phone);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addPhoneAdditional')->with($phoneAdditional);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addEmail')->with($email);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addAddress')->with($address);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addBornAt')->with($bornAt);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addPlaceOfBirth')->with($placeOfBirth);
        $this->mockNaturalPersonBuilder->expects($this->once())->method('addPassport')->with(
            $passportSeries,
            $passportNumber,
            $passportIssuedAt,
            $passportIssuedBy,
            $passportDivisionCode,
        );
        $this->mockNaturalPersonBuilder->expects($this->once())->method('build')->willReturn($this->mockNaturalPerson);
        $naturalPerson = $this->naturalPersonFactory->createForBurialCustomer(
            $fullName,
            $phone,
            $phoneAdditional,
            $email,
            $address,
            $bornAt,
            $placeOfBirth,
            $passportSeries,
            $passportNumber,
            $passportIssuedAt,
            $passportIssuedBy,
            $passportDivisionCode,
        );
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
    }

    public function testItCreatesNaturalPersonForCustomerWithoutOptionalFields(): void
    {
        $fullName = 'Иванов Иван Иванович';
        $this->mockNaturalPersonBuilder->expects($this->once())->method('initialize')->with($fullName);
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addPhone');
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addPhoneAdditional');
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addEmail');
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addAddress');
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addBornAt');
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addPlaceOfBirth');
        $this->mockNaturalPersonBuilder->expects($this->never())->method('addPassport');
        $this->mockNaturalPersonBuilder->expects($this->once())->method('build')->willReturn($this->mockNaturalPerson);
        $naturalPerson = $this->naturalPersonFactory->createForBurialCustomer(
            $fullName,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
        $this->assertInstanceOf(NaturalPerson::class, $naturalPerson);
        $this->assertNull($naturalPerson->phone());
        $this->assertNull($naturalPerson->phoneAdditional());
        $this->assertNull($naturalPerson->email());
        $this->assertNull($naturalPerson->address());
        $this->assertNull($naturalPerson->bornAt());
        $this->assertNull($naturalPerson->placeOfBirth());
        $this->assertNull($naturalPerson->passport());
    }

    public function testItFailsToCreateNaturalPersonForCustomerWithoutFullName(): void
    {
        $this->expectExceptionForNotProvidedFullName();
        $this->naturalPersonFactory->createForBurialCustomer(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );
    }

    private function expectExceptionForNotProvidedFullName(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ФИО не указано.');
    }
}
