<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial;

use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPerson;
use Cemetery\Registrar\Domain\Model\NaturalPerson\NaturalPersonId;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPerson;
use Cemetery\Registrar\Domain\Model\Organization\JuristicPerson\JuristicPersonId;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietor;
use Cemetery\Registrar\Domain\Model\Organization\SoleProprietor\SoleProprietorId;
use Cemetery\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\Burial\CustomerIdType;
use Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types\AbstractCustomJsonTypeTest;
use Doctrine\DBAL\Types\ConversionException;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdTypeTest extends AbstractCustomJsonTypeTest
{
    protected string $className                                  = CustomerIdType::class;
    protected string $typeName                                   = 'customer_id';
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат декодированного значения для ID';

    private array $phpValueClassNames = [
        NaturalPersonId::class,
        JuristicPersonId::class,
        SoleProprietorId::class,
    ];

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToDatabaseValue(
        string                                            $dbValue,
        NaturalPersonId|JuristicPersonId|SoleProprietorId $phpValue
    ): void {
        $resultingDbValue = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $this->assertJson($resultingDbValue);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('type', $decodedResultDbValue);
        $this->assertArrayHasKey('value', $decodedResultDbValue);
        $this->assertSame(
            match (true) {
                $phpValue instanceof NaturalPersonId  => NaturalPerson::CLASS_SHORTCUT,
                $phpValue instanceof JuristicPersonId => JuristicPerson::CLASS_SHORTCUT,
                $phpValue instanceof SoleProprietorId => SoleProprietor::CLASS_SHORTCUT,
            },
            $decodedResultDbValue['type']
        );
        $this->assertSame($phpValue->value(), $decodedResultDbValue['value']);
    }

    /**
     * @dataProvider getConversionData
     */
    public function testItConvertsToPhpValue(
        string                                            $dbValue,
        NaturalPersonId|JuristicPersonId|SoleProprietorId $phpValue
    ): void
    {
        /** @var NaturalPersonId|JuristicPersonId|SoleProprietorId $resultingPhpValue */
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(\get_class($phpValue), $resultingPhpValue);
        $this->assertSame($phpValue->value(), $resultingPhpValue->value());
    }

    public function testItFailsToConvertPhpValueOfInvalidTypeToDatabaseValue(): void
    {
        $valueOfInvalidType = new \stdClass();
        $this->expectException(ConversionException::class);
        $this->expectExceptionMessage(\sprintf(
            'Could not convert PHP value of type %s to type %s. Expected one of the following types: null, %s',
            \get_class($valueOfInvalidType),
            $this->typeName,
            ...$this->phpValueClassNames,
        ));
        $this->type->convertToDatabaseValue($valueOfInvalidType, $this->mockPlatform);
    }

    protected function getConversionData(): iterable
    {
        // database value, PHP value
        yield ['{"type":"NATURAL_PERSON","value":"NP001"}',  new NaturalPersonId('NP001')];
        yield ['{"type":"JURISTIC_PERSON","value":"JP001"}', new JuristicPersonId('JP001')];
        yield ['{"type":"SOLE_PROPRIETOR","value":"SP001"}', new SoleProprietorId('SP001')];
    }
}
