<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class CustomStringTypeTest extends CustomTypeTest
{
    protected string $dbValue;
    protected object $phpValue;

    public function testItConvertsToDatabaseValue(): void
    {
        $dbValue = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);

        $this->assertIsString($dbValue);
        $this->assertSame($this->dbValue, $dbValue);
    }

    public function testItConvertsToPhpValue(): void
    {
        $phpValue = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);

        $this->assertInstanceOf(\get_class($this->phpValue), $phpValue);
        $this->assertSame($this->dbValue, $phpValue->value());
    }
}
