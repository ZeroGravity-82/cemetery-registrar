<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\AbstractEntity;
use Cemetery\Registrar\Domain\AbstractEntityCollection;
use Cemetery\Registrar\Domain\AbstractEntityId;
use PHPUnit\Framework\TestCase;

/**
 * Derived classes must instantiate the collection with the only entity A.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class AbstractEntityCollectionTest extends TestCase
{
    protected AbstractEntityId $idA;

    protected AbstractEntityId $idB;

    protected AbstractEntityId $idC;

    protected AbstractEntityId $idD;

    protected AbstractEntity $entityA;

    protected AbstractEntity $entityB;

    protected AbstractEntity $entityC;

    protected AbstractEntity $entityD;

    protected AbstractEntityCollection $collection;

    abstract public function testItReturnsEntityClassName(): void;

    public function testItCountable(): void
    {
        $this->assertInstanceOf(\Countable::class, $this->collection);
    }

    public function testItIterable(): void
    {
        $this->assertInstanceOf(\IteratorAggregate::class, $this->collection);
    }

    public function testItAddsEntities(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->add($this->entityC);

        $this->assertContains($this->entityB, $this->collection);
        $this->assertContains($this->entityC, $this->collection);
    }

    public function testItReturnsAnEntityById(): void
    {
        $this->collection->add($this->entityB);

        $this->assertSame($this->entityA, $this->collection->get($this->idA));
        $this->assertSame($this->entityB, $this->collection->get($this->idB));
    }

    public function testItFailsWhenAnEntityIsNotFound(): void
    {
        $this->collection->add($this->entityB);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\sprintf(
            'Entity of type "%s" with ID "%s" is not found.',
            $this->collection->supportedEntityClassName(),
            $this->idC
        ));
        $this->collection->get($this->idC);
    }

    public function testItChecksThatAnEntityIsContained(): void
    {
        $this->collection->add($this->entityB);

        $this->assertTrue($this->collection->contains($this->entityB));
        $this->assertFalse($this->collection->contains($this->entityC));
    }

    public function testItRemovesAnEntity(): void
    {
        $this->collection->add($this->entityA);
        $this->collection->remove($this->entityA);

        $this->assertEmpty($this->collection);
    }

    public function testItReturnsFilteredCollection(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->add($this->entityC);
        $this->collection->add($this->entityD);

        $filteredCollection = $this->collection->filter($this->getClosureForCollectionFiltering());

        $this->assertCount(2, $filteredCollection);
        $this->assertContains($this->entityB, $filteredCollection);
        $this->assertContains($this->entityC, $filteredCollection);
        $this->assertNotSame($this->collection, $filteredCollection);
    }

    public function testItCanBeCleared(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->clear();

        $this->assertEmpty($this->collection);
    }

    public function testItChecksIsEmpty(): void
    {
        $this->collection->add($this->entityB);

        $this->assertFalse($this->collection->isEmpty());

        $this->collection->remove($this->entityA);
        $this->collection->remove($this->entityB);
        $this->assertTrue($this->collection->isEmpty());
    }

    public function testItReturnsACollectionKeys(): void
    {
        $this->collection->add($this->entityB);

        $this->assertContains((string) $this->idA, $this->collection->keys());
        $this->assertContains((string) $this->idB, $this->collection->keys());
    }

    public function testItReturnsACollectionValues(): void
    {
        $this->collection->add($this->entityB);

        $this->assertContains($this->entityA, $this->collection->values());
        $this->assertContains($this->entityB, $this->collection->values());
    }

    public function testItReturnsFirstEntity(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->add($this->entityC);

        $this->assertSame($this->entityA, $this->collection->first());
    }

    public function testItReturnsNullInsteadOfFirstEntity(): void
    {
        $this->collection->remove($this->entityA);
        $this->assertNull($this->collection->first());
    }

    public function testItReturnsNextEntity(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->add($this->entityC);

        $this->assertSame($this->entityB, $this->collection->next());
        $this->assertSame($this->entityC, $this->collection->next());
    }

    public function testItReturnsNullInsteadOfNextEntity(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->next();

        $this->assertNull($this->collection->next());
    }

    public function testItReturnsCurrentEntity(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->add($this->entityC);

        $this->assertSame($this->entityA, $this->collection->current());
        $this->collection->next();
        $this->assertSame($this->entityB, $this->collection->current());
        $this->assertSame($this->entityB, $this->collection->current());
        $this->collection->next();
        $this->assertSame($this->entityC, $this->collection->current());
    }

    public function testItReturnsNullInsteadOfCurrentEntity(): void
    {
        $this->collection->remove($this->entityA);
        $this->assertNull($this->collection->current());

        $this->collection->add($this->entityB);
        $this->collection->next();
        $this->collection->next();

        $this->assertNull($this->collection->current());
    }

    public function testItReturnsLastEntity(): void
    {
        $this->collection->add($this->entityB);
        $this->collection->add($this->entityC);

        $this->assertSame($this->entityC, $this->collection->last());
    }

    public function testItReturnsNullInsteadOfLastEntity(): void
    {
        $this->collection->remove($this->entityA);
        $this->assertNull($this->collection->last());
    }

    public function testItFailsWhenCreatedWithInvalidEntityType(): void
    {
        $entity      = $this->getFakeEntity();
        $entityClass = \get_class($entity);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Invalid type for an entity: expected "%s", "%s" given',
            $this->collection->supportedEntityClassName(),
            $entityClass
        ));
        $collectionClass = \get_class($this->collection);
        new $collectionClass([$entity]);
    }

    public function testItFailsWhenAddsAnEntityOfInvalidType(): void
    {
        $entity      = $this->getFakeEntity();
        $entityClass = \get_class($entity);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            'Invalid type for an entity: expected "%s", "%s" given',
            $this->collection->supportedEntityClassName(),
            $entityClass
        ));
        $this->collection->add($entity);
    }

    abstract protected function getClosureForCollectionFiltering(): \Closure;

    private function getFakeEntity(): AbstractEntity
    {
        return new class ($this->entityA->id()) extends AbstractEntity {
            public function __construct
            (
                private AbstractEntityId $id,
            ) {}

            public function id(): AbstractEntityId
            {
                return $this->id;
            }
        };
    }
}
