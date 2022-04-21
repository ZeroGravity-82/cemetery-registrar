<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\AggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractAggregateRootTest extends AbstractEntityTest
{
    public function testItIsAnAggregateRoot(): void
    {
        $this->assertInstanceOf(AggregateRoot::class, $this->entity);
    }
}
