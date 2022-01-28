<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PassportTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
        $this->assertSame('1234', $passport->getSeries());
        $this->assertSame('567890', $passport->getNumber());
        $this->assertSame('2001-01-01', $passport->getIssuedAt()->format('Y-m-d'));
        $this->assertSame(
            'DIA of the Kirovsky district of the city of Novosibirsk',
            $passport->getIssuedBy()
        );
    }

    public function testItFailsWithEmptySeriesValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Passport series value cannot be empty string.');
        new Passport(
            '',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
    }

    public function testItFailsWithEmptyNumberValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Passport number value cannot be empty string.');
        new Passport(
            '1234',
            '',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
    }

    public function testItFailsWithFutureIssuedAtValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Passport cannot be issued in the future.');
        new Passport(
            '1234',
            '567890',
            (new \DateTimeImmutable())->modify('+1 day'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
    }

    public function testItFailsWithEmptyIssuedByValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Passport issued by value cannot be empty string.');
        new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            '',
        );
    }

    public function testItStringifyable(): void
    {
        $passport = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
        $this->assertSame(
            '1234 567890, issued at 01.01.2001 by DIA of the Kirovsky district of the city of Novosibirsk',
            (string) $passport
        );
    }

    public function testItComparable(): void
    {
        $passportA = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
        $passportB = new Passport(
            '1235',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
        $passportC = new Passport(
            '1234',
            '567891',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
        $passportD = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-02'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );
        $passportE = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Dzerzhinsky district of the city of Novosibirsk',
        );
        $passportF = new Passport(
            '1234',
            '567890',
            new \DateTimeImmutable('2001-01-01'),
            'DIA of the Kirovsky district of the city of Novosibirsk',
        );

        $this->assertFalse($passportA->isEqual($passportB));
        $this->assertFalse($passportA->isEqual($passportC));
        $this->assertFalse($passportA->isEqual($passportD));
        $this->assertFalse($passportA->isEqual($passportE));
        $this->assertTrue($passportA->isEqual($passportF));
        $this->assertFalse($passportB->isEqual($passportC));
        $this->assertFalse($passportB->isEqual($passportD));
        $this->assertFalse($passportB->isEqual($passportE));
        $this->assertFalse($passportB->isEqual($passportF));
        $this->assertFalse($passportC->isEqual($passportD));
        $this->assertFalse($passportC->isEqual($passportE));
        $this->assertFalse($passportC->isEqual($passportF));
        $this->assertFalse($passportD->isEqual($passportE));
        $this->assertFalse($passportD->isEqual($passportF));
        $this->assertFalse($passportE->isEqual($passportF));
    }
}
