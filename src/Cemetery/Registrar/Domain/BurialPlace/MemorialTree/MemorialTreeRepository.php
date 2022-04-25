<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\MemorialTree;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface MemorialTreeRepository
{
    /**
     * Adds the memorial tree to the repository. If the memorial tree already exists, it will be updated.
     *
     * @param MemorialTree $memorialTree
     */
    public function save(MemorialTree $memorialTree): void;

    /**
     * Adds the collection of memorial trees to the repository. If any of the memorial trees already exist, they will
     * be updated.
     *
     * @param MemorialTreeCollection $memorialTrees
     */
    public function saveAll(MemorialTreeCollection $memorialTrees): void;

    /**
     * Returns the memorial tree by the ID. If no memorial tree found, null will be returned.
     *
     * @param MemorialTreeId $memorialTreeId
     *
     * @return MemorialTree|null
     */
    public function findById(MemorialTreeId $memorialTreeId): ?MemorialTree;

    /**
     * Removes the memorial tree from the repository.
     *
     * @param MemorialTree $memorialTree
     */
    public function remove(MemorialTree $memorialTree): void;

    /**
     * Removes the collection of memorial trees from the repository.
     *
     * @param MemorialTreeCollection $memorialTrees
     */
    public function removeAll(MemorialTreeCollection $memorialTrees): void;
}
