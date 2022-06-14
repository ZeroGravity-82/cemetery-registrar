<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Organization\BankDetails\CurrentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CurrentAccountTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $currentAccount = new CurrentAccount('40602810700000000025');
        $this->assertSame('40602810700000000025', $currentAccount->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new CurrentAccount('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new CurrentAccount('   ');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять только из цифр.');
        new CurrentAccount('4060281070000000002A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new CurrentAccount('4060281070000000002');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new CurrentAccount('406028107000000000251');
    }

    public function testItStringifyable(): void
    {
        $currentAccount = new CurrentAccount('40602810700000000025');
        $this->assertSame('40602810700000000025', (string) $currentAccount);
    }

    public function testItComparable(): void
    {
        $currentAccountA = new CurrentAccount('40602810700000000025');
        $currentAccountB = new CurrentAccount('40702810544070111112');
        $currentAccountC = new CurrentAccount('40602810700000000025');

        $this->assertFalse($currentAccountA->isEqual($currentAccountB));
        $this->assertTrue($currentAccountA->isEqual($currentAccountC));
        $this->assertFalse($currentAccountB->isEqual($currentAccountC));
    }

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять из 20 цифр.');
    }
}
