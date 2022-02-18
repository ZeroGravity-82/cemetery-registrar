<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankName;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankNameTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $bankName = new BankName('АО "Тинькофф Банк"');
        $this->assertSame($bankName->getValue(), 'АО "Тинькофф Банк"');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Наименование не может иметь пустое значение.');
        new BankName('');
    }

    public function testItStringifyable(): void
    {
        $bankName = new BankName('АО "Тинькофф Банк"');
        $this->assertSame('АО "Тинькофф Банк"', (string) $bankName);
    }

    public function testItComparable(): void
    {
        $bankNameA = new BankName('АО "Тинькофф Банк"');
        $bankNameB = new BankName('Филиал "Новосибирский" АО "Альфа-Банк"');
        $bankNameC = new BankName('АО "Тинькофф Банк"');

        $this->assertFalse($bankNameA->isEqual($bankNameB));
        $this->assertTrue($bankNameA->isEqual($bankNameC));
        $this->assertFalse($bankNameB->isEqual($bankNameC));
    }
}
