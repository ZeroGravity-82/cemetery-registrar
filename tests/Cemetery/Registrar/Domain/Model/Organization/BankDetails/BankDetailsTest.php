<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\Bik;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\CorrespondentAccount;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\CurrentAccount;
use Cemetery\Registrar\Domain\Model\Organization\Name;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsTest extends TestCase
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
        // Current account opened with a credit institution
        $bankDetails = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
        $this->assertInstanceOf(Name::class, $bankDetails->bankName());
        $this->assertSame($this->bankNameA, $bankDetails->bankName()->value());
        $this->assertInstanceOf(Bik::class, $bankDetails->bik());
        $this->assertSame($this->bikA, $bankDetails->bik()->value());
        $this->assertInstanceOf(CorrespondentAccount::class, $bankDetails->correspondentAccount());
        $this->assertSame($this->correspondentAccountA, $bankDetails->correspondentAccount()->value());
        $this->assertInstanceOf(CurrentAccount::class, $bankDetails->currentAccount());
        $this->assertSame($this->currentAccountA1, $bankDetails->currentAccount()->value());

        // Current account opened in the cash settlement center (belongs to Central Bank of Russia)
        $bankDetails = new BankDetails(
            $this->bankNameC,
            $this->bikC,
            $this->correspondentAccountC,
            $this->currentAccountC,
        );
        $this->assertInstanceOf(Name::class, $bankDetails->bankName());
        $this->assertSame($this->bankNameC, $bankDetails->bankName()->value());
        $this->assertInstanceOf(Bik::class, $bankDetails->bik());
        $this->assertSame($this->bikC, $bankDetails->bik()->value());
        $this->assertNull($bankDetails->correspondentAccount());
        $this->assertInstanceOf(CurrentAccount::class, $bankDetails->currentAccount());
        $this->assertSame($this->currentAccountC, $bankDetails->currentAccount()->value());
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

    public function testItFailsWithCorrespondentAccountMismatchedTheBikValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('К/счёт недействителен (не соответствует БИК).');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountB,
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithCurrentAccountMismatchedTheBikValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Р/счёт недействителен (не соответствует БИК).');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountB,
        );
    }

    public function testItFailsWithCorrespondentAccountProvidedForCentralBankOfRussia(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('К/счёт не может быть указан для данного БИК.');
        new BankDetails(
            $this->bankNameC,
            $this->bikC,        // BIK value of the Central Bank of Russia
            $this->correspondentAccountB,
            $this->currentAccountC,
        );
    }

    public function testItStringifyable(): void
    {
        $bankDetails = new BankDetails(
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
            (string) $bankDetails
        );
    }

    public function testItStringifyableWithoutCorrespondentAccount(): void
    {
        $bankDetails = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            null,
            $this->currentAccountA1,
        );
        $this->assertSame(
            \sprintf(
                '%s, р/счёт %s, БИК %s',
                $this->bankNameA,
                $this->currentAccountA1,
                $this->bikA,
            ),
            (string) $bankDetails
        );
    }

    public function testItComparable(): void
    {
        $bankDetailsA = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
        $bankDetailsB = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA2,
        );
        $bankDetailsC = new BankDetails(
            $this->bankNameB,
            $this->bikB,
            $this->correspondentAccountB,
            $this->currentAccountB,
        );
        $bankDetailsD = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
        $bankDetailsE = new BankDetails(
            $this->bankNameC,
            $this->bikC,
            $this->correspondentAccountC,
            $this->currentAccountC,
        );

        $this->assertFalse($bankDetailsA->isEqual($bankDetailsB));
        $this->assertFalse($bankDetailsA->isEqual($bankDetailsC));
        $this->assertTrue($bankDetailsA->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsC));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsC->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsA->isEqual($bankDetailsE));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            \sprintf('%s не может иметь пустое значение.', $name)
        );
    }
}
