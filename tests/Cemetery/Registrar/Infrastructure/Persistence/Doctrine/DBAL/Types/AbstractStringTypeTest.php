<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractStringTypeTest extends TestCase
{
    protected MockObject|AbstractPlatform $mockPlatform;

    protected string $className;

    protected string $typeName;

    private StringType $type;

    protected mixed $dbValue;

    protected mixed $phpValue;

    public function setUp(): void
    {
        $this->mockPlatform = $this->createMock(AbstractPlatform::class);
        $this->type         = new $this->className();
    }

    public function testItReturnsValidTypeName(): void
    {
        $this->assertSame($this->typeName, $this->type->getName());
    }

    public function testItRegistersType(): void
    {
        \call_user_func([$this->className, 'registerType']);
        $isTypeRegistered = \call_user_func_array([$this->className, 'hasType'], [$this->typeName]);

        $this->assertTrue($isTypeRegistered);
    }

    public function testItConvertsToDatabaseValue(): void
    {
        $dbValue = $this->type->convertToDatabaseValue($this->phpValue, $this->mockPlatform);

        $this->assertSame($this->dbValue, $dbValue);
    }

    public function testItConvertsToPhpValue(): void
    {
        $phpValue = $this->type->convertToPHPValue($this->dbValue, $this->mockPlatform);

        $this->assertInstanceOf(\get_class($this->phpValue), $phpValue);
        $this->assertSame($this->dbValue, $phpValue->getValue());
    }

    public function testItRequiresSqlCommentHint(): void
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->mockPlatform));
    }
}
