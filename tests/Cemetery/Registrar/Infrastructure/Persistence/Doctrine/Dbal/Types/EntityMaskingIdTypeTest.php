<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

use Cemetery\Registrar\Domain\EntityMaskingId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingIdTypeTest extends CustomJsonTypeTest
{
    protected string $exceptionMessageForDatabaseIncompleteValue = 'Неверный формат идентификатора';

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToDatabaseValue(string $dbValue, EntityMaskingId $phpValue): void
    {
        $resultingDbValue     = $this->type->convertToDatabaseValue($phpValue, $this->mockPlatform);
        $decodedResultDbValue = \json_decode($resultingDbValue, true);
        $this->assertIsArray($decodedResultDbValue);
        $this->assertArrayHasKey('type', $decodedResultDbValue);
        $this->assertArrayHasKey('value', $decodedResultDbValue);
        $this->assertSame($phpValue->idType(), $decodedResultDbValue['type']);
        $this->assertSame($phpValue->id()->value(), $decodedResultDbValue['value']);
    }

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToPhpValue(string $dbValue, EntityMaskingId $phpValue): void
    {
        /** @var EntityMaskingId $resultingPhpValue */
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(\get_class($phpValue), $resultingPhpValue);
        $this->assertSame($phpValue->idType(), $resultingPhpValue->idType());
        $this->assertSame($phpValue->id()->value(), $resultingPhpValue->id()->value());
    }
}
