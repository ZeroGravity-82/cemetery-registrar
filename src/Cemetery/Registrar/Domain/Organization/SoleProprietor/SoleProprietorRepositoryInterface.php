<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\SoleProprietor;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface SoleProprietorRepositoryInterface
{
    /**
     * Adds the sole proprietor to the repository. If the sole proprietor already exists, it will be updated.
     *
     * @param SoleProprietor $soleProprietor
     */
    public function save(SoleProprietor $soleProprietor): void;

    /**
     * Adds the collection of sole proprietors to the repository. If any of the sole proprietors already exist, they
     * will be updated.
     *
     * @param SoleProprietorCollection $soleProprietors
     */
    public function saveAll(SoleProprietorCollection $soleProprietors): void;

    /**
     * Returns the sole proprietor by the ID. If no sole proprietor found, null will be returned.
     *
     * @param SoleProprietorId $soleProprietorId
     *
     * @return SoleProprietor|null
     */
    public function findById(SoleProprietorId $soleProprietorId): ?SoleProprietor;

    /**
     * Removes the sole proprietor from the repository.
     *
     * @param SoleProprietor $soleProprietor
     */
    public function remove(SoleProprietor $soleProprietor): void;

    /**
     * Removes the collection of sole proprietors from the repository.
     *
     * @param SoleProprietorCollection $soleProprietors
     */
    public function removeAll(SoleProprietorCollection $soleProprietors): void;
}
