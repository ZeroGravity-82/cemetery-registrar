<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Domain\Organization\BankDetails\BankName;
use Cemetery\Registrar\Domain\Organization\BankDetails\Bik;
use Cemetery\Registrar\Domain\Organization\BankDetails\CorrespondentAccount;
use Cemetery\Registrar\Domain\Organization\BankDetails\CurrentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsTest extends TestCase
{
    private string $bankNameA;
    private string $bankNameB;
    private string $bikA;
    private string $bikB;
    private string $correspondentAccountA;
    private string $correspondentAccountB;
    private string $currentAccountA1;
    private string $currentAccountA2;
    private string $currentAccountB;

    public function setUp(): void
    {
        $this->bankNameA             = 'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"';
        $this->bankNameB             = 'Сибирский банк ПАО Сбербанк, г. Новосибирск';
        $this->bankNameC             = 'ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ';
        $this->bikA                  = '045004816';
        $this->bikB                  = '045004641';
        $this->bikC                  = '044106001';
        $this->correspondentAccountA = '30101810500000000816';
        $this->correspondentAccountB = '30101810500000000641';
        $this->correspondentAccountC = null;
        $this->currentAccountA1      = '40702810904000040651';
        $this->currentAccountA2      = '40702810304000039741';
        $this->currentAccountB       = '40702810544070111112';
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
        $this->assertInstanceOf(BankName::class, $bankDetails->getBankName());
        $this->assertSame($this->bankNameA, (string) $bankDetails->getBankName());
        $this->assertInstanceOf(Bik::class, $bankDetails->getBik());
        $this->assertSame($this->bikA, (string) $bankDetails->getBik());
        $this->assertInstanceOf(CorrespondentAccount::class, $bankDetails->getCorrespondentAccount());
        $this->assertSame($this->correspondentAccountA, (string) $bankDetails->getCorrespondentAccount());
        $this->assertInstanceOf(CurrentAccount::class, $bankDetails->getCurrentAccount());
        $this->assertSame($this->currentAccountA1, (string) $bankDetails->getCurrentAccount());

        // Current account opened in the cash settlement center (belongs to Central Bank of Russia)
        $bankDetails = new BankDetails(
            $this->bankNameC,
            $this->bikC,
            $this->correspondentAccountC,
            $this->currentAccountC,
        );
        $this->assertInstanceOf(BankName::class, $bankDetails->getBankName());
        $this->assertSame($this->bankNameC, (string) $bankDetails->getBankName());
        $this->assertInstanceOf(Bik::class, $bankDetails->getBik());
        $this->assertSame($this->bikC, (string) $bankDetails->getBik());
        $this->assertNull($bankDetails->getCorrespondentAccount());
        $this->assertInstanceOf(CurrentAccount::class, $bankDetails->getCurrentAccount());
        $this->assertSame($this->currentAccountC, (string) $bankDetails->getCurrentAccount());
    }

    public function testItFailsWithEmptyBankNameValue(): void
    {
        $this->expectExceptionForEmptyValue('Наименование банка');
        new BankDetails(
            '',
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA1,
        );
    }

    public function testItFailsWithBankNameValueOfSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue('Наименование банка');
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
        $this->expectException(\InvalidArgumentException::class);
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
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт недействителен (не соответствует БИК).');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountB,
        );
    }

    public function testItFailsWithCorrespondentAccountProvidedForBikValueOfCentralBankOfRussia(): void
    {
        $this->expectException(\InvalidArgumentException::class);
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

        $this->assertFalse($bankDetailsA->isEqual($bankDetailsB));
        $this->assertFalse($bankDetailsA->isEqual($bankDetailsC));
        $this->assertTrue($bankDetailsA->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsC));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsC->isEqual($bankDetailsD));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf('%s не может иметь пустое значение.', $name)
        );
    }
}
