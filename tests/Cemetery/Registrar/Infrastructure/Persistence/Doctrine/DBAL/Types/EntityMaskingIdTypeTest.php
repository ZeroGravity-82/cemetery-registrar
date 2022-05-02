<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Cemetery\Registrar\Domain\EntityId;
use Cemetery\Registrar\Domain\EntityMaskingId;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityMaskingIdTypeTest extends JsonTypeTest
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
        $this->assertArrayHasKey('class', $decodedResultDbValue);
        $this->assertArrayHasKey('value', $decodedResultDbValue);
        $this->assertSame($this->getClass($phpValue->id()), $decodedResultDbValue['class']);
        $this->assertSame($phpValue->id()->value(), $decodedResultDbValue['value']);
    }

    /**
     * @dataProvider getConversionTests
     */
    public function testItConvertsToPhpValue(string $dbValue, EntityMaskingId $phpValue): void
    {
        $resultingPhpValue = $this->type->convertToPHPValue($dbValue, $this->mockPlatform);
        $this->assertInstanceOf(\get_class($phpValue), $resultingPhpValue);
        $this->assertSame($this->getClass($phpValue->id()), $this->getClass($resultingPhpValue->id()));
        $this->assertSame($phpValue->id()->value(), $resultingPhpValue->id()->value());
    }

    private function getClass(EntityId $id): string
    {
        $parts = \explode('\\', \get_class($id));

        return \end($parts);
    }
}
