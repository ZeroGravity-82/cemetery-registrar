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
        $this->passportIssuedAtA     = new \DateTimeImmutable('2002-10-28');
        $this->passportIssuedAtB     = new \DateTimeImmutable('2011-03-23');
        $this->passportIssuedByA     = 'УВД Кировского района города Новосибирска';
        $this->passportIssuedByB     = 'Отделом УФМС России по Новосибирской области в Заельцовском районе';
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
        $this->assertSame($this->passportSeriesA, $passport->series());
        $this->assertSame($this->passportNumberA, $passport->number());
        $this->assertSame($this->passportIssuedAtA->format('Y-m-d'), $passport->issuedAt()->format('Y-m-d'));
        $this->assertSame(
            $this->passportIssuedByA,
            $passport->issuedBy()
        );
        $this->assertSame($this->passportDivisionCodeA, $passport->divisionCode());
    }

    public function testItFailsWithEmptySeriesValue(): void
    {
        $this->expectExceptionForEmptyValue('Серия паспорта');
        new Passport(
            '',
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithSeriesValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Серия паспорта');
        new Passport(
            '   ',
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithEmptyNumberValue(): void
    {
        $this->expectExceptionForEmptyValue('Номер паспорта');
        new Passport(
            $this->passportSeriesA,
            '',
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithNumberValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Номер паспорта');
        new Passport(
            $this->passportSeriesA,
            '   ',
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
    }

    public function testItFailsWithFutureIssuedAtValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Дата выдачи паспорта не может иметь значение из будущего.');
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
        $this->expectExceptionForEmptyValue('Наименование органа, выдавшего паспорт,');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            '',
            null,
        );
    }

    public function testItFailsWithIssuedByValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Наименование органа, выдавшего паспорт,');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            '   ',
            null,
        );
    }

    public function testItFailsWithEmptyDivisionCodeValue(): void
    {
        $this->expectExceptionForEmptyValue('Код подразделения');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            '',
        );
    }

    public function testItFailsWithDivisionCodeValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Код подразделения');
        new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            '   ',
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
                'Паспорт серия %s номер %s, выдан %s %s (код подразделения %s)',
                $this->passportSeriesA,
                $this->passportNumberA,
                $this->passportIssuedByA,
                $this->passportIssuedAtA->format('d.m.Y'),
                $this->passportDivisionCodeA,
            ),
            (string) $passport
        );
    }

    public function testItStringifyableWithoutDivisionCode(): void
    {
        $passport = new Passport(
            $this->passportSeriesA,
            $this->passportNumberA,
            $this->passportIssuedAtA,
            $this->passportIssuedByA,
            null,
        );
        $this->assertSame(
            \sprintf(
                'Паспорт серия %s номер %s, выдан %s %s',
                $this->passportSeriesA,
                $this->passportNumberA,
                $this->passportIssuedByA,
                $this->passportIssuedAtA->format('d.m.Y'),
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
            \sprintf('%s не может иметь пустое значение.', $name)
        );
    }
}
