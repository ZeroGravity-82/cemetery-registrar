<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface DeceasedRepositoryInterface
{
    /**
     * Adds the deceased to the repository. If the deceased already exists, it will be updated.
     *
     * @param Deceased $deceased
     */
    public function save(Deceased $deceased): void;

    /**
     * Adds the collection of deceased to the repository. If any of the deceased already exist, they will be updated.
     *
     * @param DeceasedCollection $deceased
     */
    public function saveAll(DeceasedCollection $deceased): void;

    /**
     * Returns the deceased by the ID. If no deceased found, null will be returned.
     *
     * @param DeceasedId $deceasedId
     *
     * @return Deceased|null
     */
    public function findById(DeceasedId $deceasedId): ?Deceased;

    /**
     * Removes the deceased from the repository.
     *
     * @param Deceased $deceased
     */
    public function remove(Deceased $deceased): void;

    /**
     * Removes the collection of deceased from the repository.
     *
     * @param DeceasedCollection $deceased
     */
    public function removeAll(DeceasedCollection $deceased): void;
}
