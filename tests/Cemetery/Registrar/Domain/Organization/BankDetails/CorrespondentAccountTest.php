<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\Bik;
use Cemetery\Registrar\Domain\Organization\BankDetails\CorrespondentAccount;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CorrespondentAccountTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $correspondentAccount = new CorrespondentAccount('30101810600000000774', new Bik('045004774'));
        $this->assertSame($correspondentAccount->getValue(), '30101810600000000774');
        $this->assertInstanceOf(Bik::class, $correspondentAccount->getBik());
        $this->assertSame('045004774', (string) $correspondentAccount->getBik());
    }

    public function testItFailsWithEmptyValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт не может иметь пустое значение.');
        new CorrespondentAccount('', new Bik('045004774'));
    }

    public function testItFailsWithNonNumericValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт должен состоять только из цифр.');
        new CorrespondentAccount('3010181060000000077A', new Bik('045004774'));
    }

    public function testItFailsWithTooShortValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт должен состоять из 20 цифр.');
        new CorrespondentAccount('3010181060000000077', new Bik('045004774'));
    }

    public function testItFailsWithTooLongValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт должен состоять из 20 цифр.');
        new CorrespondentAccount('301018106000000007741', new Bik('045004774'));
    }

    public function testItFailsWithWrongBikValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт недействителен (не соответствует БИК).');
        new CorrespondentAccount('30101810600000000774', new Bik('044525974'));
    }

    public function testItFailsWithBikValueOfCentralBankOfRussia(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('К/счёт не может быть указан для данного БИК.');
        new CorrespondentAccount('30101810800000000746', new Bik('049805000'));
    }

    public function testItStringifyable(): void
    {
        $correspondentAccount = new CorrespondentAccount('30101810600000000774', new Bik('045004774'));
        $this->assertSame('30101810600000000774', (string) $correspondentAccount);
    }

    public function testItComparable(): void
    {
        $correspondentAccountA = new CorrespondentAccount('30101810600000000774', new Bik('045004774'));
        $correspondentAccountB = new CorrespondentAccount('30101810145250000974', new Bik('044525974'));
        $correspondentAccountC = new CorrespondentAccount('30101810600000000774', new Bik('045004774'));

        $this->assertFalse($correspondentAccountA->isEqual($correspondentAccountB));
        $this->assertTrue($correspondentAccountA->isEqual($correspondentAccountC));
        $this->assertFalse($correspondentAccountB->isEqual($correspondentAccountC));
    }
}
