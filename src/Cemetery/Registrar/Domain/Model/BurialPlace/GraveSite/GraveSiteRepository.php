<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface GraveSiteRepository
{
    /**
     * Adds the grave site to the repository. If the grave site is already persisted, it will be updated.
     *
     * @param GraveSite $graveSite
     */
    public function save(GraveSite $graveSite): void;

    /**
     * Adds the collection of grave sites to the repository. If any of the grave sites are already persisted, they will
     * be updated.
     *
     * @param GraveSiteCollection $graveSites
     */
    public function saveAll(GraveSiteCollection $graveSites): void;

    /**
     * Returns the grave site by the ID. If no grave site found, null will be returned.
     *
     * @param GraveSiteId $graveSiteId
     *
     * @return GraveSite|null
     */
    public function findById(GraveSiteId $graveSiteId): ?GraveSite;

    /**
     * Removes the grave site from the repository.
     *
     * @param GraveSite $graveSite
     */
    public function remove(GraveSite $graveSite): void;

    /**
     * Removes the collection of grave sites from the repository.
     *
     * @param GraveSiteCollection $graveSites
     */
    public function removeAll(GraveSiteCollection $graveSites): void;
}
