<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\NaturalPerson\DeceasedDetails;

use Cemetery\Registrar\Domain\Model\NaturalPerson\DeceasedDetails\DeceasedDetails;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class DeceasedDetailsTest extends TestCase
{
    private string  $bankNameA;
    private string  $bankNameB;
    private string  $bankNameC;
    private string  $bikA;
    private string  $bikB;
    private string  $bikC;
    private string  $correspondentAccountA;
    private string  $correspondentAccountB;
    private ?string $correspondentAccountC;
    private string  $currentAccountA1;
    private string  $currentAccountA2;
    private string  $currentAccountB;
    private string  $currentAccountC;

    public function setUp(): void
    {
        $this->bankNameA             = 'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"';
        $this->bankNameB             = 'АО "АЛЬФА-БАНК"';
        $this->bankNameC             = 'ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ';
        $this->bikA                  = '045004816';
        $this->bikB                  = '044525593';
        $this->bikC                  = '044106001';
        $this->correspondentAccountA = '30101810500000000816';
        $this->correspondentAccountB = '30101810200000000593';
        $this->correspondentAccountC = null;
        $this->currentAccountA1      = '40702810904000040651';
        $this->currentAccountA2      = '40702810304000039741';
        $this->currentAccountB       = '40701810401400000014';
        $this->currentAccountC       = '40601810900001000022';
    }

    public function testItSuccessfullyCreated(): void
    {
        $deceasedDetails = new DeceasedDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
        $this->assertInstanceOf(Name::class, $deceasedDetails->bankName());
        $this->assertSame($this->bankNameA, (string) $deceasedDetails->bankName());
        $this->assertInstanceOf(Bik::class, $deceasedDetails->bik());
        $this->assertSame($this->bikA, (string) $deceasedDetails->bik());
        $this->assertInstanceOf(CorrespondentAccount::class, $deceasedDetails->correspondentAccount());
        $this->assertSame($this->correspondentAccountA, (string) $deceasedDetails->correspondentAccount());
        $this->assertInstanceOf(CurrentAccount::class, $deceasedDetails->currentAccount());
        $this->assertSame($this->currentAccountA1, (string) $deceasedDetails->currentAccount());
    }

    public function testItFailsWithEmptyBankNameValue(): void
    {
        $this->expectExceptionForEmptyValue('Наименование');
        new BankDetails(
            '',
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithBankNameValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Наименование');
        new BankDetails(
            '   ',
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithEmptyBikValue(): void
    {
        $this->expectExceptionForEmptyValue('БИК');
        new BankDetails(
            $this->bankNameA,
            '',
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithBikValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('БИК');
        new BankDetails(
            $this->bankNameA,
            '   ',
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithEmptyCorrespondentAccountValue(): void
    {
        $this->expectExceptionForEmptyValue('К/счёт');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            '',
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithCorrespondentAccountValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('К/счёт');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            '   ',
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithEmptyCurrentAccountValue(): void
    {
        $this->expectExceptionForEmptyValue('Р/счёт');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            '',
        );
    }

    public function testItFailsWithCurrentAccountValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Р/счёт');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            '   ',
        );
    }

    public function testItStringifyable(): void
    {
        $deceasedDetails = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
        $this->assertSame(
            \sprintf(
                '%s, р/счёт %s, к/счёт %s, БИК %s',
                $this->bankNameA,
                $this->currentAccountA1,
                $this->correspondentAccountA,
                $this->bikA,
            ),
            (string) $deceasedDetails
        );
    }

    public function testItComparable(): void
    {
        $deceasedDetailsA = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
        $deceasedDetailsB = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA2,
        );
        $deceasedDetailsC = new BankDetails(
            $this->bankNameB,
            $this->bikB,
            $this->correspondentAccountB,
            $this->currentAccountB,
        );
        $deceasedDetailsD = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );

        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsB));
        $this->assertFalse($deceasedDetailsA->isEqual($deceasedDetailsC));
        $this->assertTrue($deceasedDetailsA->isEqual($deceasedDetailsD));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsC));
        $this->assertFalse($deceasedDetailsB->isEqual($deceasedDetailsD));
        $this->assertFalse($deceasedDetailsC->isEqual($deceasedDetailsD));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            \sprintf('%s не может иметь пустое значение.', $name)
        );
    }
}
