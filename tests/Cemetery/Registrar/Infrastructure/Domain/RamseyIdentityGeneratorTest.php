<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Infrastructure\Domain;

use Cemetery\Registrar\Infrastructure\Domain\RamseyIdentityGenerator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class RamseyIdentityGeneratorTest extends TestCase
{
    public function testItReturnsNextIdentity(): void
    {
        $mockUuidFactory = $this->createMock(UuidFactory::class);
        $generator       = new RamseyIdentityGenerator($mockUuidFactory);

        $this->assertIsString($generator->getNextIdentity());
    }
}
