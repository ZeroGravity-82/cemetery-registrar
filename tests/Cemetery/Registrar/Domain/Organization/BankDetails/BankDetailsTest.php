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
    private string $currentAccountA;
    private string $currentAccountB;

    public function setUp(): void
    {
        $this->bankNameA             = 'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"';
        $this->bankNameB             = 'Сибирский банк ПАО Сбербанк, г. Новосибирск';
        $this->bikA                  = '045004816';
        $this->bikB                  = '045004641';
        $this->correspondentAccountA = '30101810500000000816';
        $this->correspondentAccountB = '30101810500000000641';
        $this->currentAccountA       = '40702810904111040651';
        $this->currentAccountB       = '40702810544222111112';
    }

    public function testItSuccessfullyCreated(): void
    {
        $bankDetails = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $this->assertInstanceOf(BankName::class, $bankDetails->getBankName());
        $this->assertSame($this->bankNameA, (string) $bankDetails->getBankName());
        $this->assertInstanceOf(Bik::class, $bankDetails->getBik());
        $this->assertSame($this->bikA, (string) $bankDetails->getBik());
        $this->assertInstanceOf(CorrespondentAccount::class, $bankDetails->getCorrespondentAccount());
        $this->assertSame($this->correspondentAccountA, (string) $bankDetails->getCorrespondentAccount());
        $this->assertInstanceOf(CurrentAccount::class, $bankDetails->getCurrentAccount());
        $this->assertSame($this->currentAccountA, (string) $bankDetails->getCurrentAccount());
    }

    public function testItFailsWithEmptyBankNameValue(): void
    {
        $this->expectExceptionForEmptyValue('наименование банка');
        new BankDetails(
            '',
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
    }

    public function testItFailsWithEmptyBikValue(): void
    {
        $this->expectExceptionForEmptyValue('БИК');
        new BankDetails(
            $this->bankNameA,
            '',
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
    }

    public function testItFailsWithEmptyCorrespondentAccountValue(): void
    {
        $this->expectExceptionForEmptyValue('к/счёт');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            '',
            $this->currentAccountA,
        );
    }

    public function testItFailsWithEmptyCurrentAccountValue(): void
    {
        $this->expectExceptionForEmptyValue('р/счёт');
        new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            '',
        );
    }

    public function testItStringifyable(): void
    {
        $bankDetails = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $this->assertSame(
            \sprintf(
                '%s, р/счёт %s, к/счёт %s, БИК %s',
                $this->bankNameA,
                $this->currentAccountA,
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
            $this->currentAccountA,
        );
        $bankDetailsB = new BankDetails(
            $this->bankNameB,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $bankDetailsC = new BankDetails(
            $this->bankNameA,
            $this->bikB,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $bankDetailsD = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountB,
            $this->currentAccountA,
        );
        $bankDetailsE = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountB,
        );
        $bankDetailsF = new BankDetails(
            $this->bankNameA,
            $this->bikA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );

        $this->assertFalse($bankDetailsA->isEqual($bankDetailsB));
        $this->assertFalse($bankDetailsA->isEqual($bankDetailsC));
        $this->assertFalse($bankDetailsA->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsA->isEqual($bankDetailsE));
        $this->assertTrue($bankDetailsA->isEqual($bankDetailsF));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsC));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsE));
        $this->assertFalse($bankDetailsB->isEqual($bankDetailsF));
        $this->assertFalse($bankDetailsC->isEqual($bankDetailsD));
        $this->assertFalse($bankDetailsC->isEqual($bankDetailsE));
        $this->assertFalse($bankDetailsC->isEqual($bankDetailsF));
        $this->assertFalse($bankDetailsD->isEqual($bankDetailsE));
        $this->assertFalse($bankDetailsD->isEqual($bankDetailsF));
        $this->assertFalse($bankDetailsE->isEqual($bankDetailsF));
    }

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf('%s не может иметь пустое значение.', \ucfirst($name))
        );
    }
}
