<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model;

use Cemetery\Registrar\Domain\Model\AbstractAggregateRoot;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractAggregateRootTest extends AbstractEntityTest
{
    public function testItIsAggregateRoot(): void
    {
        $this->assertInstanceOf(AbstractAggregateRoot::class, $this->entity);
    }
}
