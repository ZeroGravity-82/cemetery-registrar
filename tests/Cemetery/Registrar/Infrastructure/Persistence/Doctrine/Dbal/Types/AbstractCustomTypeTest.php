<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Persistence\Doctrine\Dbal\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractCustomTypeTest extends TestCase
{
    protected MockObject|AbstractPlatform $mockPlatform;
    protected string                      $className;
    protected string                      $typeName;
    protected Type                        $type;

    public function setUp(): void
    {
        $this->mockPlatform = $this->createMock(AbstractPlatform::class);
        $this->type         = new $this->className();
    }

    public function testItReturnsValidTypeName(): void
    {
        $this->assertSame($this->typeName, $this->type->getName());
    }

    public function testItRequiresSqlCommentHint(): void
    {
        $this->assertTrue($this->type->requiresSQLCommentHint($this->mockPlatform));
    }
}
