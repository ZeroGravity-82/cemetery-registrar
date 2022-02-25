<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\Bik;
use Cemetery\Registrar\Domain\Organization\BankDetails\CurrentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CurrentAccountTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        // Current account opened with a credit institution
        $currentAccount = new CurrentAccount('40602810700000000025', new Bik('042908762'));
        $this->assertSame('40602810700000000025', $currentAccount->getValue());
        $this->assertInstanceOf(Bik::class, $currentAccount->getBik());
        $this->assertSame('042908762', (string) $currentAccount->getBik());

        // Current account opened in the cash settlement center (belongs to Central Bank of Russia)
        $currentAccount = new CurrentAccount('40701810000001002118', new Bik('044106001'));
        $this->assertSame('40701810000001002118', $currentAccount->getValue());
        $this->assertInstanceOf(Bik::class, $currentAccount->getBik());
        $this->assertSame('044106001', (string) $currentAccount->getBik());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт не может иметь пустое значение.');
        new CurrentAccount('', new Bik('042908762'));
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять только из цифр.');
        new CurrentAccount('4060281070000000002A', new Bik('042908762'));
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new CurrentAccount('4060281070000000002', new Bik('042908762'));
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new CurrentAccount('406028107000000000251', new Bik('042908762'));
    }

    public function testItFailsWithWrongBikValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт недействителен (не соответствует БИК).');
        new CurrentAccount('40602810700000000025', new Bik('045004641'));
    }

    public function testItStringifyable(): void
    {
        $currentAccount = new CurrentAccount('40602810700000000025', new Bik('042908762'));
        $this->assertSame('40602810700000000025', (string) $currentAccount);
    }

    public function testItComparable(): void
    {
        $currentAccountA = new CurrentAccount('40602810700000000025', new Bik('042908762'));
        $currentAccountB = new CurrentAccount('40702810544070111112', new Bik('045004641'));
        $currentAccountC = new CurrentAccount('40602810700000000025', new Bik('042908762'));

        $this->assertFalse($currentAccountA->isEqual($currentAccountB));
        $this->assertTrue($currentAccountA->isEqual($currentAccountC));
        $this->assertFalse($currentAccountB->isEqual($currentAccountC));
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять из 20 цифр.');
    }
}
