<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Deceased;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface DeceasedRepository
{
    /**
     * Adds the deceased to the repository. If the deceased is already persisted, it will be updated.
     *
     * @param Deceased $deceased
     */
    public function save(Deceased $deceased): void;

    /**
     * Adds the collection of deceaseds to the repository. If any of the deceaseds are already persisted, they will be
     * updated.
     *
     * @param DeceasedCollection $deceaseds
     */
    public function saveAll(DeceasedCollection $deceaseds): void;

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
     * Removes the collection of deceaseds from the repository.
     *
     * @param DeceasedCollection $deceaseds
     */
    public function removeAll(DeceasedCollection $deceaseds): void;
}
