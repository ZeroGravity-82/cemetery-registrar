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
    private string             $passportSeriesA;
    private string             $passportSeriesB;
    private string             $passportNumberA;
    private string             $passportNumberB;
    private \DateTimeImmutable $passportIssuedAtA;
    private \DateTimeImmutable $passportIssuedAtB;
    private string             $passportIssuedByA;
    private string             $passportIssuedByB;
    private string             $passportDivisionCodeA;
    private string             $passportDivisionCodeB;

    public function setUp(): void
    {
        $this->passportSeriesA       = '1234';
        $this->passportSeriesB       = '1235';
        $this->passportNumberA       = '567890';
        $this->passportNumberB       = '567891';
        $this->passportIssuedAtA     = new \DateTimeImmutable('2001-01-01');
        $this->passportIssuedAtB     = new \DateTimeImmutable('2001-01-02');
        $this->passportIssuedByA     = 'DIA of the Kirovsky district of the city of Novosibirsk';
        $this->passportIssuedByB     = 'DIA of the Dzerzhinsky district of the city of Novosibirsk';
        $this->passportDivisionCodeA = '540-001';
        $this->passportDivisionCodeB = '541-001';
    }

    public function testItSuccessfullyCreated(): void
    {
        $passport = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $this->assertSame($this->passportSeriesA, $passport->getSeries());
        $this->assertSame($this->passportNumberA, $passport->getNumber());
        $this->assertSame($this->passportIssuedAtA->format('Y-m-d'), $passport->getIssuedAt()->format('Y-m-d'));
        $this->assertSame(
            $this->passportIssuedByA,
            $passport->getIssuedBy()
        );
        $this->assertSame($this->passportDivisionCodeA, $passport->getDivisionCode());
    }

    public function testItFailsWithEmptySeriesValue(): void
    {
        $this->expectExceptionForEmptyValue('passport series');
        new Passport(
            '',
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithNullSeriesValue(): void
    {
        $this->expectExceptionForEmptyValue('passport series');
        new Passport(
            null,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithEmptyNumberValue(): void
    {
        $this->expectExceptionForEmptyValue('passport number');
        new Passport(
            $this->passportSeriesA,
            '',
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithNullNumberValue(): void
    {
        $this->expectExceptionForEmptyValue('passport number');
        new Passport(
            $this->passportSeriesA,
            null,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithFutureIssuedAtValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Passport cannot be issued in the future.');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            (new \DateTimeImmutable())->modify('+1 day'),
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithEmptyIssuedByValue(): void
    {
        $this->expectExceptionForEmptyValue('passport issued by');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            '',
            null,
        );
    }

    public function testItFailsWithNullIssuedByValue(): void
    {
        $this->expectExceptionForEmptyValue('passport issued by');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            null,
            null,
        );
    }

    public function testItFailsWithEmptyDivisionCode(): void
    {
        $this->expectExceptionForEmptyValue('division code');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            '',
        );
    }

    public function testItStringifyable(): void
    {
        $passport = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $this->assertSame(
            \sprintf(
                '%s %s, issued at %s by %s (division code %s)',
                $this->passportSeriesA,
                $this->passportNumberA,
                $this->passportIssuedAtA->format('d.m.Y'),
                $this->passportIssuedByA,
                $this->passportDivisionCodeA,
            ),
            (string) $passport
        );
    }

    public function testItComparable(): void
    {
        $passportA = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $passportB = new Passport(
            $this->passportSeriesB,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $passportC = new Passport(
            $this->passportSeriesA,
            $this->passportNumberB,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $passportD = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtB,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $passportE = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByB,
            $this->passportDivisionCodeA,
        );
        $passportF = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeA,
        );
        $passportG = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            $this->passportDivisionCodeB,
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
        $this->assertFalse($passportF->isEqual($passportG));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf('%s value cannot be empty.', ucfirst($name))
        );
    }
}
