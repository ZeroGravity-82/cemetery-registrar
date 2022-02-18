<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CurrentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CurrentAccountTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $currentAccount = new CurrentAccount('40701810000001002118');
        $this->assertSame($currentAccount->getValue(), '40701810000001002118');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт не может иметь пустое значение.');
        new CurrentAccount('');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять только из цифр.');
        new CurrentAccount('4070181000000100211A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять из 20 цифр.');
        new CurrentAccount('4070181000000100211');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Р/счёт должен состоять из 20 цифр.');
        new CurrentAccount('407018100000010021181');
    }

    public function testItStringifyable(): void
    {
        $currentAccount = new CurrentAccount('40701810000001002118');
        $this->assertSame('40701810000001002118', (string) $currentAccount);
    }

    public function testItComparable(): void
    {
        $currentAccountA = new CurrentAccount('40701810000001002118');
        $currentAccountB = new CurrentAccount('30232810100000000004');
        $currentAccountC = new CurrentAccount('40701810000001002118');

        $this->assertFalse($currentAccountA->isEqual($currentAccountB));
        $this->assertTrue($currentAccountA->isEqual($currentAccountC));
        $this->assertFalse($currentAccountB->isEqual($currentAccountC));
    }
}
