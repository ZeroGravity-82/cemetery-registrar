<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\CorrespondentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CorrespondentAccountTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $correspondentAccount = new CorrespondentAccount('30101810600000000774');
        $this->assertSame($correspondentAccount->getValue(), '30101810600000000774');
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт не может иметь пустое значение.');
        new CorrespondentAccount('');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт должен состоять только из цифр.');
        new CorrespondentAccount('3010181060000000077A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт должен состоять из 20 цифр.');
        new CorrespondentAccount('3010181060000000077');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт должен состоять из 20 цифр.');
        new CorrespondentAccount('301018106000000007741');
    }

    public function testItStringifyable(): void
    {
        $correspondentAccount = new CorrespondentAccount('30101810600000000774');
        $this->assertSame('30101810600000000774', (string) $correspondentAccount);
    }

    public function testItComparable(): void
    {
        $correspondentAccountA = new CorrespondentAccount('30101810600000000774');
        $correspondentAccountB = new CorrespondentAccount('30101810145250000974');
        $correspondentAccountC = new CorrespondentAccount('30101810600000000774');

        $this->assertFalse($correspondentAccountA->isEqual($correspondentAccountB));
        $this->assertTrue($correspondentAccountA->isEqual($correspondentAccountC));
        $this->assertFalse($correspondentAccountB->isEqual($correspondentAccountC));
    }
}
