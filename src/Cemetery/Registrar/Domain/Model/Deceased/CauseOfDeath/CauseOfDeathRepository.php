<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CauseOfDeathRepository
{
    /**
     * Adds the cause of death to the repository. If the cause of death is already persisted, it will be updated.
     *
     * @param CauseOfDeath $causeOfDeath
     */
    public function save(CauseOfDeath $causeOfDeath): void;

    /**
     * Adds the collection of causes of death to the repository. If any of the causes of death are already persisted,
     * they will be updated.
     *
     * @param CauseOfDeathCollection $causesOfDeath
     */
    public function saveAll(CauseOfDeathCollection $causesOfDeath): void;

    /**
     * Returns the cause of death by the ID. If no cause of death found, null will be returned.
     *
     * @param CauseOfDeathId $causeOfDeathId
     *
     * @return CauseOfDeath|null
     */
    public function findById(CauseOfDeathId $causeOfDeathId): ?CauseOfDeath;

    /**
     * Removes the cause of death from the repository.
     *
     * @param CauseOfDeath $causeOfDeath
     */
    public function remove(CauseOfDeath $causeOfDeath): void;

    /**
     * Removes the collection of causes of death from the repository.
     *
     * @param CauseOfDeathCollection $causesOfDeath
     */
    public function removeAll(CauseOfDeathCollection $causesOfDeath): void;
}
