<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use Cemetery\Tests\Registrar\Domain\AbstractAggregateRootTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonTest extends AbstractAggregateRootTest
{
    private NaturalPerson $naturalPerson;
    
    public function setUp(): void
    {
        $naturalPersonId     = new NaturalPersonId('777');
        $fullName            = new FullName('Иванов Иван Иванович');
        $this->naturalPerson = new NaturalPerson($naturalPersonId, $fullName);
        $this->entity        = $this->naturalPerson;
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(NaturalPersonId::class, $this->naturalPerson->id());
        $this->assertSame('777', (string) $this->naturalPerson->id());
        $this->assertInstanceOf(FullName::class, $this->naturalPerson->fullName());
        $this->assertSame('Иванов Иван Иванович', (string) $this->naturalPerson->fullName());
        $this->assertNull($this->naturalPerson->phone());
        $this->assertNull($this->naturalPerson->phoneAdditional());
        $this->assertNull($this->naturalPerson->email());
        $this->assertNull($this->naturalPerson->address());
        $this->assertNull($this->naturalPerson->bornAt());
        $this->assertNull($this->naturalPerson->placeOfBirth());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->createdAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->createdAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->updatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->updatedAt());
    }

    public function testItSetsPhone(): void
    {
        $phone = '+7-913-777-88-99';
        $this->naturalPerson->setPhone($phone);
        $this->assertSame('+7-913-777-88-99', $this->naturalPerson->phone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phone = '+7-913-555-66-77';
        $this->naturalPerson->setPhoneAdditional($phone);
        $this->assertSame('+7-913-555-66-77', $this->naturalPerson->phoneAdditional());
    }

    public function testItSetsEmail(): void
    {
        $email = 'info@example.com';
        $this->naturalPerson->setEmail($email);
        $this->assertSame('info@example.com', $this->naturalPerson->email());
    }

    public function testItSetsAddress(): void
    {
        $address = 'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37';
        $this->naturalPerson->setAddress($address);
        $this->assertSame('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37', $this->naturalPerson->address());
    }

    public function testItSetsBornAt(): void
    {
        $bornAt = new \DateTimeImmutable('2000-01-01');
        $this->naturalPerson->setBornAt($bornAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->bornAt());
        $this->assertSame('2000-01-01', $this->naturalPerson->bornAt()->format('Y-m-d'));
    }

    public function testItSetsPlaceOfBirth(): void
    {
        $placeOfBirth = 'город Новосибирск';
        $this->naturalPerson->setPlaceOfBirth($placeOfBirth);
        $this->assertSame('город Новосибирск', $this->naturalPerson->placeOfBirth());
    }

    public function testItSetsPassport(): void
    {
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'УВД Кировского района города Новосибирска',
            '540-001',
        );
        $this->naturalPerson->setPassport($passport);
        $this->assertInstanceOf(Passport::class, $this->naturalPerson->passport());
        $this->assertSame('1234', $this->naturalPerson->passport()->series());
        $this->assertSame('567890', $this->naturalPerson->passport()->number());
        $this->assertSame('2001-01-01', $this->naturalPerson->passport()->issuedAt()->format('Y-m-d'));
        $this->assertSame('УВД Кировского района города Новосибирска', $this->naturalPerson->passport()->issuedBy());
        $this->assertSame('540-001', $this->naturalPerson->passport()->divisionCode());
    }
}
