<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumNicheRepository
{
    /**
     * Adds the columbarium niche to the repository. If the columbarium niche already exists, it will be updated.
     *
     * @param ColumbariumNiche $columbariumNiche
     */
    public function save(ColumbariumNiche $columbariumNiche): void;

    /**
     * Adds the collection of columbarium niches to the repository. If any of the columbarium niches already exist,
     * they will be updated.
     *
     * @param ColumbariumNicheCollection $columbariumNiches
     */
    public function saveAll(ColumbariumNicheCollection $columbariumNiches): void;

    /**
     * Returns the columbarium niche by the ID. If no columbarium niche found, null will be returned.
     *
     * @param ColumbariumNicheId $columbariumNicheId
     *
     * @return ColumbariumNiche|null
     */
    public function findById(ColumbariumNicheId $columbariumNicheId): ?ColumbariumNiche;

    /**
     * Removes the columbarium niche from the repository.
     *
     * @param ColumbariumNiche $columbariumNiche
     */
    public function remove(ColumbariumNiche $columbariumNiche): void;

    /**
     * Removes the collection of columbarium niches from the repository.
     *
     * @param ColumbariumNicheCollection $columbariumNiches
     */
    public function removeAll(ColumbariumNicheCollection $columbariumNiches): void;
}
