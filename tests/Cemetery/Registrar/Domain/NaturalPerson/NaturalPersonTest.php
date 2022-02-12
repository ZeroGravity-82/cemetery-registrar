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
        $fullName            = new FullName('Ivanov Ivan Ivanovich');
        $this->naturalPerson = new NaturalPerson($naturalPersonId, $fullName);
    }
    
    public function testItSuccessfullyCreated(): void
    {
        $this->assertInstanceOf(NaturalPersonId::class, $this->naturalPerson->getId());
        $this->assertSame('777', (string) $this->naturalPerson->getId());
        $this->assertInstanceOf(FullName::class, $this->naturalPerson->getFullName());
        $this->assertSame('Ivanov Ivan Ivanovich', (string) $this->naturalPerson->getFullName());
        $this->assertNull($this->naturalPerson->getBornAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getCreatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getUpdatedAt());
        $this->assertLessThan(new \DateTimeImmutable(), $this->naturalPerson->getUpdatedAt());
    }
    
    public function testItSetsBornAt(): void
    {
        $bornAt = new \DateTimeImmutable('2000-01-01');
        $this->naturalPerson->setBornAt($bornAt);
        $this->assertInstanceOf(\DateTimeImmutable::class, $this->naturalPerson->getBornAt());
        $this->assertSame('2000-01-01', $this->naturalPerson->getBornAt()->format('Y-m-d'));
    }

    public function testItSetsPassport(): void
    {
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
            '540-001',
        );
        $this->naturalPerson->setPassport($passport);
        $this->assertInstanceOf(Passport::class, $this->naturalPerson->getPassport());
        $this->assertSame('1234', $this->naturalPerson->getPassport()->getSeries());
        $this->assertSame('567890', $this->naturalPerson->getPassport()->getNumber());
        $this->assertSame('2001-01-01', $this->naturalPerson->getPassport()->getIssuedAt()->format('Y-m-d'));
        $this->assertSame('DIA of the Kirovsky district of the city of Novosibirsk', $this->naturalPerson->getPassport()->getIssuedBy());
        $this->assertSame('540-001', $this->naturalPerson->getPassport()->getDivisionCode());
    }
}
