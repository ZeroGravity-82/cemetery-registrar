<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Burial;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface BurialRepositoryInterface
{
    /**
     * Adds the burial to the repository. If the burial already exists, it will be updated.
     *
     * @param Burial $burial
     */
    public function save(Burial $burial): void;

    /**
     * Adds the collection of burials to the repository. If any of the burials already exist, they will be updated.
     *
     * @param BurialCollection $burials
     */
    public function saveAll(BurialCollection $burials): void;

    /**
     * Returns the burial by the ID. If no burial found, null will be returned.
     *
     * @param BurialId $burialId
     *
     * @return Burial|null
     */
    public function findById(BurialId $burialId): ?Burial;

    /**
     * Removes the burial from the repository.
     *
     * @param Burial $burial
     */
    public function remove(Burial $burial): void;

    /**
     * Removes the collection of burials from the repository.
     *
     * @param BurialCollection $burials
     */
    public function removeAll(BurialCollection $burials): void;
}
