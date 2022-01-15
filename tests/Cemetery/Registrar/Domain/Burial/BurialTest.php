<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Burial;

use Cemetery\Registrar\Domain\EntityId;
use PHPUnit\Framework\TestCase;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class BurialTest extends TestCase
{


    public function testItSuccessfullyCreated(): void
    {
        $burialId      = new EntityId('777');
        $deceasedId    = new EntityId('888');







        $customerId    = new CustomerId('natural_person', '999');








        $siteOwnerType = 'natural_person';
        $siteOwnerId   = new EntityId('AAA');

        $siteId        = new EntityId('BBB');
        $burial        = new Burial($burialId);

        $this->assertInstanceOf(BurialId::class, $burial->getId());
        $this->assertSame('777', (string) $burial->getId());
    }
}
