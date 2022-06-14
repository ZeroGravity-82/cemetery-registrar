<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model;

use Cemetery\Registrar\Domain\Model\IdentityGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityFactoryTest extends TestCase
{
    protected const ENTITY_ID = '555';

    protected MockObject|IdentityGenerator $mockIdentityGenerator;

    public function setUp(): void
    {
        $this->mockIdentityGenerator = $this->createMock(IdentityGenerator::class);
        $this->mockIdentityGenerator->method('getNextIdentity')->willReturn(self::ENTITY_ID);
    }
}
