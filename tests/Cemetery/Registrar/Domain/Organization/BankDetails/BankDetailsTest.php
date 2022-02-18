<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsTest extends TestCase
{
    private string $bankNameA;
    private string $bankNameB;
    private string $rcbicA;
    private string $rcbicB;
    private string $correspondentAccountA;
    private string $correspondentAccountB;
    private string $currentAccountA;
    private string $currentAccountB;

    public function setUp(): void
    {
        $this->bankNameA             = 'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"';
        $this->bankNameB             = 'Сибирский банк ПАО Сбербанк, г. Новосибирск';
        $this->rcbicA                = '045004816';
        $this->rcbicB                = '045004641';
        $this->correspondentAccountA = '30101810500000000816';
        $this->correspondentAccountB = '30101810500000000641';
        $this->currentAccountA       = '40702810904111040651';
        $this->currentAccountB       = '40702810544222111112';
    }

    public function testItSuccessfullyCreated(): void
    {
        $bankDetails = new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $this->assertSame($this->bankNameA, $bankDetails->getBankName());
        $this->assertSame($this->rcbicA, $bankDetails->getRcbic());
        $this->assertSame($this->correspondentAccountA, $bankDetails->getCorrespondentAccount());
        $this->assertSame($this->currentAccountA, $bankDetails->getCurrentAccount());
    }

    public function testItFailsWithEmptyBankNameValue(): void
    {
        $this->expectExceptionForEmptyValue('наименование банка');
        new BankDetails(
            '',
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
    }

    public function testItFailsWithEmptyRcbicValue(): void
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
        $this->expectExceptionForEmptyValue('корреспондентский счёт');
        new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            '',
            $this->currentAccountA,
        );
    }

    public function testItFailsWithEmptyCurrentAccountValue(): void
    {
        $this->expectExceptionForEmptyValue('расчётный счёт');
        new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountA,
            '',
        );
    }

    public function testItStringifyable(): void
    {
        $bankDetails = new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $this->assertSame(
            \sprintf(
                '%s, р/счёт %s, к/счёт %s, БИК %s',
                $this->bankNameA,
                $this->currentAccountA,
                $this->correspondentAccountA,
                $this->rcbicA,
            ),
            (string) $bankDetails
        );
    }

    public function testItComparable(): void
    {
        $passportA = new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $passportB = new BankDetails(
            $this->bankNameB,
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $passportC = new BankDetails(
            $this->bankNameA,
            $this->rcbicB,
            $this->correspondentAccountA,
            $this->currentAccountA,
        );
        $passportD = new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountB,
            $this->currentAccountA,
        );
        $passportE = new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountB,
        );
        $passportF = new BankDetails(
            $this->bankNameA,
            $this->rcbicA,
            $this->correspondentAccountA,
            $this->currentAccountA,
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

    private function expectExceptionForEmptyValue(string $name): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            \sprintf('%s не может иметь пустое значение.', \ucfirst($name))
        );
    }
}
