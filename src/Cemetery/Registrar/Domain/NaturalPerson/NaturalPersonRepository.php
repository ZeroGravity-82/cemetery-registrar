<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\NaturalPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface NaturalPersonRepository
{
    /**
     * Adds the natural person to the repository. If the natural person is already persisted, it will be updated.
     *
     * @param NaturalPerson $naturalPerson
     */
    public function save(NaturalPerson $naturalPerson): void;

    /**
     * Adds the collection of natural persons to the repository. If any of the natural persons are already persisted,
     * they will be updated.
     *
     * @param NaturalPersonCollection $naturalPersons
     */
    public function saveAll(NaturalPersonCollection $naturalPersons): void;

    /**
     * Returns the natural person by the ID. If no natural person found, null will be returned.
     *
     * @param NaturalPersonId $naturalPersonId
     *
     * @return NaturalPerson|null
     */
    public function findById(NaturalPersonId $naturalPersonId): ?NaturalPerson;

    /**
     * Removes the natural person from the repository.
     *
     * @param NaturalPerson $naturalPerson
     */
    public function remove(NaturalPerson $naturalPerson): void;

    /**
     * Removes the collection of natural persons from the repository.
     *
     * @param NaturalPersonCollection $naturalPersons
     */
    public function removeAll(NaturalPersonCollection $naturalPersons): void;
}
