<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Model\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails\BankDetailsType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsTypeTest extends AbstractCustomJsonTypeTest
{
    protected string $className                                  = BankDetailsType::class;
    protected string $typeName                                   = 'bank_details';
    protected string $phpValueClassName                          = BankDetails::class;
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат декодированного значения для банковских реквизитов';

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToDatabaseValue(string $dbValue, BankDetails $phpValue): void
    {
        $resultingDbValue = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $this->assertJson($resultingDbValue);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('bankName', $decodedResultDbValue);
        $this->assertArrayHasKey('bik', $decodedResultDbValue);
        $this->assertArrayHasKey('correspondentAccount', $decodedResultDbValue);
        $this->assertArrayHasKey('currentAccount', $decodedResultDbValue);
        $this->assertSame($phpValue->bankName()->value(), $decodedResultDbValue['bankName']);
        $this->assertSame($phpValue->bik()->value(), $decodedResultDbValue['bik']);
        $this->assertSame($phpValue->correspondentAccount()?->value(), $decodedResultDbValue['correspondentAccount']);
        $this->assertSame($phpValue->currentAccount()->value(), $decodedResultDbValue['currentAccount']);
    }

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToPhpValue(string $dbValue, BankDetails $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(BankDetails::class, $resultingPhpValue);
        $this->assertTrue($resultingPhpValue->isEqual($phpValue));
    }

    protected function getConversionData(): iterable
    {
        // database value,
        // PHP value
        yield [
            <<<JSON_A
{
  "bankName": "Сибирский филиал Публичного акционерного общества \"Промсвязьбанк\"",
  "bik": "045004816",
  "correspondentAccount": "30101810500000000816",
  "currentAccount": "40702810904000040651"
}
JSON_A
            ,
            new BankDetails(
                'Сибирский филиал Публичного акционерного общества "Промсвязьбанк"',
                '045004816',
                '30101810500000000816',
                '40702810904000040651',
            )
        ];
        yield [
            <<<JSON_B
{
  "bankName": "ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ",
  "bik": "044106001",
  "correspondentAccount": null,
  "currentAccount": "40601810900001000022"
}
JSON_B
            ,
            new BankDetails(
                'ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ',
                '044106001',
                null,
                '40601810900001000022',
            )
        ];
    }
}
