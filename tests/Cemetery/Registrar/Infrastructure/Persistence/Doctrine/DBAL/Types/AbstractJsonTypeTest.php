<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractJsonTypeTest extends AbstractTypeTest
{
    public function testItConvertsToDatabaseValue(): void
    {
        $dbValue = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);

        $decodedDbValue = \json_decode($dbValue, true);
        $this->assertIsArray($decodedDbValue);
        $this->assertArrayHasKey('type', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertSame($this->phpValue->getIdType(), $decodedDbValue['type']);
        $this->assertSame($this->phpValue->getId()->getValue(), $decodedDbValue['value']);
    }

    public function testItConvertsToPhpValue(): void
    {
        $phpValue = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);

        $decodedDbValue = \json_decode($this->dbValue, true);
        $this->assertArrayHasKey('type', $decodedDbValue);
        $this->assertArrayHasKey('value', $decodedDbValue);
        $this->assertInstanceOf(\get_class($this->phpValue), $phpValue);
        $this->assertSame($decodedDbValue['type'], $phpValue->getIdType());
        $this->assertSame($decodedDbValue['value'], $phpValue->getId()->getValue());
    }
}
