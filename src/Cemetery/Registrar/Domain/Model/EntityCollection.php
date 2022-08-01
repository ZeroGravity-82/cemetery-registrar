<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
abstract class EntityCollection implements \Countable, \IteratorAggregate
{
    private array $entities = [];

    /**
     * @throws \LogicException when the entity type does not match the collection
     */
    public function __construct(
        array $entities = [],
    ) {
        foreach ($entities as $entity) {
            $this->assertSupportedEntityClass($entity);
            $this->add($entity);
        }
    }

    abstract public function supportedEntityClassName(): string;

    public function count(): int
    {
        return \count($this->entities);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->entities);
    }

    /**
     * @throws \LogicException when the entity type does not match the collection
     */
    public function add(Entity $entity): void
    {
        $this->assertSupportedEntityClass($entity);
        $entityId                  = (string) $entity->id();
        $this->entities[$entityId] = $entity;
    }

    /**
     * @throws \LogicException when the entity is not found by ID
     */
    public function get(EntityId $entityId): Entity
    {
        $entityId = (string) $entityId;
        if (!isset($this->entities[$entityId])) {
            throw new \LogicException(\sprintf(
                'Сущность с ID "%s" и типом "%s" не найдена.',
                $entityId,
                $this->supportedEntityClassName(),
            ));
        }

        return $this->entities[$entityId];
    }

    public function contains(Entity $entity): bool
    {
        return \in_array($entity, $this->entities, true);
    }

    public function remove(Entity $entity): void
    {
        $entityId = (string) $entity->id();
        unset($this->entities[$entityId]);
    }

    /**
     * Returns all the entities of this collection that satisfy the predicate p. The order of the entities is preserved.
     */
    public function filter(\Closure $p): static
    {
        return new static(\array_filter($this->entities, $p));
    }

    public function clear(): void
    {
        $this->entities = [];
    }

    public function isEmpty(): bool
    {
        return empty($this->entities);
    }

    /**
     * Returns all keys (entity IDs) of the collection.
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
     */
    public function values(): array
    {
        return \array_values($this->entities);
    }

    public function first(): ?Entity
    {
        $first = \reset($this->entities);

        return $first ?: null;
    }

    public function next(): ?Entity
    {
        $next = \next($this->entities);

        return $next ?: null;
    }

    public function current(): ?Entity
    {
        $current = \current($this->entities);

        return $current ?: null;
    }

    public function last(): ?Entity
    {
        $last = \end($this->entities);

        return $last ?: null;
    }

    /**
     * @throws \LogicException when the entity type does not match the collection
     */
    private function assertSupportedEntityClass(Entity $entity): void
    {
        $supportedEntityClassName = $this->supportedEntityClassName();
        if (!$entity instanceof $supportedEntityClassName) {
            throw new \LogicException(\sprintf(
                'Неподдерживаемый тип сущности: ожидался "%s", "%s" передан.',
                $this->supportedEntityClassName(),
                \get_class($entity)
            ));
        }
    }
}
