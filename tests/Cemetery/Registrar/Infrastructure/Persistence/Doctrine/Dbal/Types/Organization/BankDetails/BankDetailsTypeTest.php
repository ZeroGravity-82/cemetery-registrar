<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails;

use Cemetery\Registrar\Domain\Organization\BankDetails\BankDetails;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Organization\BankDetails\BankDetailsType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BankDetailsTypeTest extends CustomJsonTypeTest
{
    protected string $className                                  = BankDetailsType::class;
    protected string $typeName                                   = 'bank_details';
    protected string $phpValueClassName                          = BankDetails::class;
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат банковских реквизитов';

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToDatabaseValue(string $dbValue, BankDetails $phpValue): void
    {
        $resultingDbValue     = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
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
     * @dataProvider getConversionTests
     */
    public function testItConvertsToPhpValue(string $dbValue, BankDetails $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(BankDetails::class, $resultingPhpValue);
        $this->assertTrue($resultingPhpValue->isEqual($phpValue));
    }

    protected function getConversionTests(): array
    {
        return [
            // database value,
            // PHP value
            [
                '{"bankName":"Сибирский филиал Публичного акционерного общества \"Промсвязьбанк\"","bik":"045004816","correspondentAccount":"30101810500000000816","currentAccount":"40702810904000040651"}',
                new BankDetails('Сибирский филиал Публичного акционерного общества "Промсвязьбанк"', '045004816', '30101810500000000816', '40702810904000040651')
            ],
            [
                '{"bankName":"ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ","bik":"044106001","correspondentAccount":null,"currentAccount":"40601810900001000022"}',
                new BankDetails('ОТДЕЛЕНИЕ ЛЕНИНГРАДСКОЕ БАНКА РОССИИ', '044106001', null, '40601810900001000022')
            ],
        ];
    }
}
