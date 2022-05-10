<?php

declare(strict_types=1);

namespace Cemetery\Tests\Registrar\Domain;

use Cemetery\Registrar\Domain\Entity;
use Cemetery\Registrar\Domain\EntityCollection;
use Cemetery\Registrar\Domain\EntityId;
use PHPUnit\Framework\TestCase;

/**
 * Derived classes must instantiate the collection with the only entity A.
 *
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityCollectionTest extends TestCase
{
    protected EntityId         $idA;
    protected EntityId         $idB;
    protected EntityId         $idC;
    protected EntityId         $idD;
    protected Entity           $entityA;
    protected Entity           $entityB;
    protected Entity           $entityC;
    protected Entity           $entityD;
    protected EntityCollection $collection;

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

    public function testItReturnsEntityById(): void
    {
        $this->collection->add($this->entityB);

        $this->assertSame($this->entityA, $this->collection->get($this->idA));
        $this->assertSame($this->entityB, $this->collection->get($this->idB));
    }

    public function testItFailsWhenEntityIsNotFound(): void
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

    public function testItChecksThatEntityIsContained(): void
    {
        $this->collection->add($this->entityB);

        $this->assertTrue($this->collection->contains($this->entityB));
        $this->assertFalse($this->collection->contains($this->entityC));
    }

    public function testItRemovesEntity(): void
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

    public function testItReturnsCollectionKeys(): void
    {
        $this->collection->add($this->entityB);

        $this->assertContains((string) $this->idA, $this->collection->keys());
        $this->assertContains((string) $this->idB, $this->collection->keys());
    }

    public function testItReturnsCollectionValues(): void
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

    public function testItFailsWhenAddsEntityOfInvalidType(): void
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

    private function getFakeEntity(): Entity
    {
        return new class ($this->entityA->id()) extends Entity {
            public function __construct
            (
                private readonly EntityId $id,
            ) {
                parent::__construct();
            }

            public function id(): EntityId
            {
                return $this->id;
            }
        };
    }
}
