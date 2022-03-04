<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface JuristicPersonRepositoryInterface
{
    /**
     * Adds the juristic person to the repository. If the juristic person already exists, it will be updated.
     *
     * @param JuristicPerson $juristicPerson
     */
    public function save(JuristicPerson $juristicPerson): void;

    /**
     * Adds the collection of juristic persons to the repository. If any of the juristic persons already exist, they
     * will be updated.
     *
     * @param JuristicPersonCollection $juristicPersons
     */
    public function saveAll(JuristicPersonCollection $juristicPersons): void;

    /**
     * Returns the juristic person by the ID. If no juristic person found, null will be returned.
     *
     * @param JuristicPersonId $juristicPersonId
     *
     * @return JuristicPerson|null
     */
    public function findById(JuristicPersonId $juristicPersonId): ?JuristicPerson;

    /**
     * Removes the juristic person from the repository.
     *
     * @param JuristicPerson $juristicPerson
     */
    public function remove(JuristicPerson $juristicPerson): void;

    /**
     * Removes the collection of juristic persons from the repository.
     *
     * @param JuristicPersonCollection $juristicPersons
     */
    public function removeAll(JuristicPersonCollection $juristicPersons): void;
}
