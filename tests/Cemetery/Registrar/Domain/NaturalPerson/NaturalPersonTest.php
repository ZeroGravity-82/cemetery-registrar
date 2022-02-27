<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\FullName;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonTest extends TestCase
{
    private NaturalPerson $naturalPerson;
    
    public function setUp(): void
    {
        $naturalPersonId     = new NaturalPersonId('777');
        $fullName            = new FullName('Иванов Иван Иванович');
        $this->naturalPerson = new NaturalPerson($naturalPersonId, $fullName);
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(NaturalPersonId::class, $this->naturalPerson->getId());
        $this->assertSame('777', (string) $this->naturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $this->naturalPerson->getFullName());
        $this->assertSame('Иванов Иван Иванович', (string) $this->naturalPerson->getFullName());
        $this->assertNull($this->naturalPerson->getPhone());
        $this->assertNull($this->naturalPerson->getPhoneAdditional());
        $this->assertNull($this->naturalPerson->getEmail());
        $this->assertNull($this->naturalPerson->getAddress());
        $this->assertNull($this->naturalPerson->getBornAt());
        $this->assertNull($this->naturalPerson->getPlaceOfBirth());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->getUpdatedAt());
    }

    public function testItSetsPhone(): void
    {
        $phone = '+7-913-777-88-99';
        $this->naturalPerson->setPhone($phone);
        $this->assertSame('+7-913-777-88-99', $this->naturalPerson->getPhone());
    }

    public function testItSetsPhoneAdditional(): void
    {
        $phone = '+7-913-555-66-77';
        $this->naturalPerson->setPhoneAdditional($phone);
        $this->assertSame('+7-913-555-66-77', $this->naturalPerson->getPhoneAdditional());
    }

    public function testItSetsEmail(): void
    {
        $email = 'info@example.com';
        $this->naturalPerson->setEmail($email);
        $this->assertSame('info@example.com', $this->naturalPerson->getEmail());
    }

    public function testItSetsAddress(): void
    {
        $address = 'г. Новосибирск, ул. Дмитрия Шамшурина, д. 37';
        $this->naturalPerson->setAddress($address);
        $this->assertSame('г. Новосибирск, ул. Дмитрия Шамшурина, д. 37', $this->naturalPerson->getAddress());
    }

    public function testItSetsBornAt(): void
    {
        $bornAt = new \DateTimeImmutable('2000-01-01');
        $this->naturalPerson->setBornAt($bornAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getBornAt());
        $this->assertSame('2000-01-01', $this->naturalPerson->getBornAt()->format('Y-m-d'));
    }

    public function testItSetsPlaceOfBirth(): void
    {
        $placeOfBirth = 'город Новосибирск';
        $this->naturalPerson->setPlaceOfBirth($placeOfBirth);
        $this->assertSame('город Новосибирск', $this->naturalPerson->getPlaceOfBirth());
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
        $this->assertInstanceOf(Passport::class, $this->naturalPerson->getPassport());
        $this->assertSame('1234', $this->naturalPerson->getPassport()->getSeries());
        $this->assertSame('567890', $this->naturalPerson->getPassport()->getNumber());
        $this->assertSame('2001-01-01', $this->naturalPerson->getPassport()->getIssuedAt()->format('Y-m-d'));
        $this->assertSame('УВД Кировского района города Новосибирска', $this->naturalPerson->getPassport()->getIssuedBy());
        $this->assertSame('540-001', $this->naturalPerson->getPassport()->getDivisionCode());
    }
}
