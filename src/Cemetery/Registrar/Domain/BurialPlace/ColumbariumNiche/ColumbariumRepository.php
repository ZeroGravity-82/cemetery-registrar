<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\BurialPlace\ColumbariumNiche;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface ColumbariumRepository
{
    /**
     * Adds the columbarium to the repository. If the columbarium is already persisted, it will be updated.
     *
     * @param Columbarium $columbarium
     */
    public function save(Columbarium $columbarium): void;

    /**
     * Adds the collection of columbariums  to the repository. If any of the columbariums are already persisted, they
     * will be updated.
     *
     * @param ColumbariumCollection $columbariums
     */
    public function saveAll(ColumbariumCollection $columbariums): void;

    /**
     * Returns the columbarium by the ID. If no columbarium found, null will be returned.
     *
     * @param ColumbariumId $columbariumId
     *
     * @return Columbarium|null
     */
    public function findById(ColumbariumId $columbariumId): ?Columbarium;

    /**
     * Removes the columbarium from the repository.
     *
     * @param Columbarium $columbarium
     */
    public function remove(Columbarium $columbarium): void;

    /**
     * Removes the collection of columbariums from the repository.
     *
     * @param ColumbariumCollection $columbariums
     */
    public function removeAll(ColumbariumCollection $columbariums): void;
}
