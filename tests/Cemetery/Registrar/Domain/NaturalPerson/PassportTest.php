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
    private string             $seriesA;
    private string             $seriesB;
    private string             $numberA;
    private string             $numberB;
    private \DateTimeImmutable $issuedAtA;
    private \DateTimeImmutable $issuedAtB;
    private string             $issuedByA;
    private string             $issuedByB;
    private string             $divisionCodeA;
    private string|null        $divisionCodeB;

    public function setUp(): void
    {
        $this->seriesA       = '1234';
        $this->seriesB       = '1235';
        $this->numberA       = '567890';
        $this->numberB       = '567891';
        $this->issuedAtA     = new \DateTimeImmutable('2002-10-28');
        $this->issuedAtB     = new \DateTimeImmutable('2011-03-23');
        $this->issuedByA     = 'УВД Кировского района города Новосибирска';
        $this->issuedByB     = 'Отделом УФМС России по Новосибирской области в Заельцовском районе';
        $this->divisionCodeA = '540-001';
        $this->divisionCodeB = null;
    }

    public function testItSuccessfullyCreated(): void
    {
        $passport = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $this->assertSame($this->seriesA, $passport->series());
        $this->assertSame($this->numberA, $passport->number());
        $this->assertSame($this->issuedAtA->format('Y-m-d'), $passport->issuedAt()->format('Y-m-d'));
        $this->assertSame(
            $this->issuedByA,
            $passport->issuedBy()
        );
        $this->assertSame($this->divisionCodeA, $passport->divisionCode());
    }

    public function testItFailsWithEmptySeriesValue(): void
    {
        $this->expectExceptionForEmptyValue('Серия паспорта');
        new Passport(
            '',
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            null,
        );
    }

    public function testItFailsWithSeriesValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Серия паспорта');
        new Passport(
            '   ',
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            null,
        );
    }

    public function testItFailsWithEmptyNumberValue(): void
    {
        $this->expectExceptionForEmptyValue('Номер паспорта');
        new Passport(
            $this->seriesA,
            '',
            $this->issuedAtA,
            $this->issuedByA,
            null,
        );
    }

    public function testItFailsWithNumberValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Номер паспорта');
        new Passport(
            $this->seriesA,
            '   ',
            $this->issuedAtA,
            $this->issuedByA,
            null,
        );
    }

    public function testItFailsWithFutureIssuedAtValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Дата выдачи паспорта не может иметь значение из будущего.');
        new Passport(
            $this->seriesA,
            $this->numberA,
            (new \DateTimeImmutable())->modify('+1 day'),
            $this->issuedByA,
            null,
        );
    }

    public function testItFailsWithEmptyIssuedByValue(): void
    {
        $this->expectExceptionForEmptyValue('Наименование органа, выдавшего паспорт,');
        new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            '',
            null,
        );
    }

    public function testItFailsWithIssuedByValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Наименование органа, выдавшего паспорт,');
        new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            '   ',
            null,
        );
    }

    public function testItFailsWithEmptyDivisionCodeValue(): void
    {
        $this->expectExceptionForEmptyValue('Код подразделения');
        new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            '',
        );
    }

    public function testItFailsWithDivisionCodeValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Код подразделения');
        new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            '   ',
        );
    }

    public function testItStringifyable(): void
    {
        $passport = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $this->assertSame(
            \sprintf(
                'Паспорт серия %s номер %s, выдан %s %s (код подразделения %s)',
                $this->seriesA,
                $this->numberA,
                $this->issuedByA,
                $this->issuedAtA->format('d.m.Y'),
                $this->divisionCodeA,
            ),
            (string) $passport
        );
    }

    public function testItStringifyableWithoutDivisionCode(): void
    {
        $passport = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            null,
        );
        $this->assertSame(
            \sprintf(
                'Паспорт серия %s номер %s, выдан %s %s',
                $this->seriesA,
                $this->numberA,
                $this->issuedByA,
                $this->issuedAtA->format('d.m.Y'),
            ),
            (string) $passport
        );
    }

    public function testItComparable(): void
    {
        $passportA = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $passportB = new Passport(
            $this->seriesB,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $passportC = new Passport(
            $this->seriesA,
            $this->numberB,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $passportD = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtB,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $passportE = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByB,
            $this->divisionCodeA,
        );
        $passportF = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeA,
        );
        $passportG = new Passport(
            $this->seriesA,
            $this->numberA,
            $this->issuedAtA,
            $this->issuedByA,
            $this->divisionCodeB,
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
