<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Exception;
use Cemetery\Registrar\Domain\Model\Organization\BankDetails\CorrespondentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CorrespondentAccountTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $correspondentAccount = new CorrespondentAccount('30101810600000000774');
        $this->assertSame('30101810600000000774', $correspondentAccount->value());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectExceptionForEmptyValue();
        new CorrespondentAccount('');
    }

    public function testItFailsWithSpacesOnly(): void
    {
        $this->expectExceptionForEmptyValue();
        new CorrespondentAccount('   ');
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('К/счёт должен состоять только из цифр.');
        new CorrespondentAccount('3010181060000000077A');
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectExceptionForInvalidLength();
        new CorrespondentAccount('3010181060000000077');
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectExceptionForInvalidLength();
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

    private function expectExceptionForEmptyValue(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('К/счёт не может иметь пустое значение.');
    }

    private function expectExceptionForInvalidLength(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('К/счёт должен состоять из 20 цифр.');
    }
}
