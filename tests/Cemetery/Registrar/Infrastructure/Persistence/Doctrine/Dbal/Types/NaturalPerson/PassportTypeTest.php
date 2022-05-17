<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson;

use Cemetery\Registrar\Domain\NaturalPerson\Passport;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\NaturalPerson\PassportType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\CustomJsonTypeTest;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class PassportTypeTest extends CustomJsonTypeTest
{
    protected string $className                                  = PassportType::class;
    protected string $typeName                                   = 'passport';
    protected string $phpValueClassName                          = Passport::class;
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат паспортных данных';

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToDatabaseValue(string $dbValue, Passport $phpValue): void
    {
        $resultingDbValue     = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('series', $decodedResultDbValue);
        $this->assertArrayHasKey('number', $decodedResultDbValue);
        $this->assertArrayHasKey('issuedAt', $decodedResultDbValue);
        $this->assertArrayHasKey('issuedBy', $decodedResultDbValue);
        $this->assertArrayHasKey('divisionCode', $decodedResultDbValue);
        $this->assertSame($phpValue->series(), $decodedResultDbValue['series']);
        $this->assertSame($phpValue->number(), $decodedResultDbValue['number']);
        $this->assertSame($phpValue->issuedAt()->format('Y-m-d'), $decodedResultDbValue['issuedAt']);
        $this->assertSame($phpValue->issuedBy(), $decodedResultDbValue['issuedBy']);
        $this->assertSame($phpValue->divisionCode(), $decodedResultDbValue['divisionCode']);
    }

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToPhpValue(string $dbValue, Passport $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(Passport::class, $resultingPhpValue);
        $this->assertTrue($resultingPhpValue->isEqual($phpValue));
    }

    protected function getConversionTests(): array
    {
        return [
            // database value,
            // PHP value
            [
                '{"series":"1234","number":"567890","issuedAt":"2002-10-28","issuedBy":"УВД Кировского района города Новосибирска","divisionCode":"540-001"}',
                new Passport('1234', '567890', new \DateTimeImmutable('2002-10-28'), 'УВД Кировского района города Новосибирска', '540-001')
            ],
            [
                '{"series":"1235","number":"567891","issuedAt":"2011-03-23","issuedBy":"Отделом УФМС России по Новосибирской области в Заельцовском районе","divisionCode":null}',
                new Passport('1235', '567891', new \DateTimeImmutable('2011-03-23'), 'Отделом УФМС России по Новосибирской области в Заельцовском районе', null)
            ],
        ];
    }
}
