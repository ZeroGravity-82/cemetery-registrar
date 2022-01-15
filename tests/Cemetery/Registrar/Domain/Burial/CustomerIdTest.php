<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\Burial\CustomerId;
use Cemetery\Registrar\Domain\EntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CustomerIdTest extends TestCase
{
    public function testItSuccessfullyCreated(): void
    {
        $customerId = new CustomerId('natural_person', new EntityId('777'));

        $this->assertInstanceOf(CustomerId::class, $customerId);
        $this->assertSame('natural_person', $customerId->getType());
        $this->assertSame('777', (string) $customerId->getId());
    }
}
