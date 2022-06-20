<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityCollection implements \Countable, \IteratorAggregate
{
    /** @var Entity[]|array */
    private array $entities = [];

    /**
     * @param array $entities
     */
    public function __construct(
        array $entities = [],
    ) {
        foreach ($entities as $entity) {
            $this->assertValidType($entity);
            $this->add($entity);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->entities);
    }

    /**
     * Adds the entity to the collection.
     *
     * @param Entity $entity
     */
    public function add(Entity $entity): void
    {
        $this->assertValidType($entity);
        $entityId                  = (string) $entity->id();
        $this->entities[$entityId] = $entity;
    }

    /**
     * Returns the entity by ID. If no entity found, then a \LogicException will be thrown.
     *
     * @param EntityId $entityId
     *
     * @return Entity
     *
     * @throws \LogicException when the entity is not found by ID
     */
    public function get(EntityId $entityId): Entity
    {
        $entityId = (string) $entityId;
        if (!isset($this->entities[$entityId])) {
            throw new \LogicException(\sprintf(
                'Entity of type "%s" with ID "%s" is not found.',
                $this->supportedClassName(),
                $entityId
            ));
        }

        return $this->entities[$entityId];
    }

    /**
     * Checks whether the entity is contained in the collection.
     *
     * @param Entity $entity
     *
     * @return bool
     */
    public function contains(Entity $entity): bool
    {
        return \in_array($entity, $this->entities, true);
    }

    /**
     * Removes the entity from the collection.
     *
     * @param Entity $entity
     */
    public function remove(Entity $entity): void
    {
        $entityId = (string) $entity->id();
        unset($this->entities[$entityId]);
    }

    /**
     * Returns all the entities of this collection that satisfy the predicate p. The order of the entities is preserved.
     *
     * @param \Closure $p
     *
     * @return static
     */
    public function filter(\Closure $p): static
    {
        return new static(\array_filter($this->entities, $p));
    }

    /**
     * Clears the collection, removing all entities.
     */
    public function clear(): void
    {
        $this->entities = [];
    }

    /**
     * Checks whether the collection is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->entities);
    }

    /**
     * Returns all keys (entity IDs) of the collection.
     *
     * @return array
     */
    public function keys(): array
    {
        return \array_map(
            function ($entityId) { return (string) $entityId; },
            \array_keys($this->entities)
        );
    }

    /**
     * Returns all values (entities) of the collection.
     *
     * @return array
     */
    public function values(): array
    {
        return \array_values($this->entities);
    }

    /**
     * Sets the internal iterator to the first entity in the collection and returns this entity.
     *
     * @return Entity|null
     */
    public function first(): ?Entity
    {
        $first = \reset($this->entities);

        return $first ?: null;
    }

    /**
     * Moves the internal iterator position to the next entity and returns this entity.
     *
     * @return Entity|null
     */
    public function next(): ?Entity
    {
        $next = \next($this->entities);

        return $next ?: null;
    }

    /**
     * Returns the entity of the collection at the current iterator position.
     *
     * @return Entity|null
     */
    public function current(): ?Entity
    {
        $current = \current($this->entities);

        return $current ?: null;
    }

    /**
     * Sets the internal iterator to the last entity in the collection and returns this entity.
     *
     * @return Entity|null
     */
    public function last(): ?Entity
    {
        $last = \end($this->entities);

        return $last ?: null;
    }

    /**
     * Returns the name of the supported entity class.
     *
     * @return string
     */
    abstract public function supportedClassName(): string;

    /**
     * Checks whether the entity is of a type supported by the collection.
     *
     * @param Entity $entity
     *
     * @throws \InvalidArgumentException when the entity type does not match the collection
     */
    private function assertValidType(Entity $entity): void
    {
        $supportedClassName = $this->supportedClassName();
        if (!$entity instanceof $supportedClassName) {
            throw new \InvalidArgumentException(\sprintf(
                'Invalid type for an entity: expected "%s", "%s" given.',
                $this->supportedClassName(),
                \get_class($entity)
            ));
        }
    }
}
