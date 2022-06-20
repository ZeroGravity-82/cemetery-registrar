<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain\Model;

use Cemetery\Registrar\Domain\Model\AggregateRoot;
use Cemetery\Registrar\Domain\Model\AggregateRootCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AggregateRootCollectionTest extends EntityCollectionTest
{
    public function testItIsAggregateRootCollection(): void
    {
        $this->assertInstanceOf(AggregateRootCollection::class, $this->collection);
    }

    public function testSupportedEntityClassIsAggregateRoot(): void
    {
        $this->assertInstanceOf(AggregateRoot::class, $this->createMock($this->collection->supportedEntityClassName()));
    }
}
