<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model;

use Cemetery\Registrar\Domain\Model\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AggregateRootTest extends EntityTest
{
    public function testItIsAggregateRoot(): void
    {
        $this->assertInstanceOf(AggregateRoot::class, $this->entity);
    }
}
