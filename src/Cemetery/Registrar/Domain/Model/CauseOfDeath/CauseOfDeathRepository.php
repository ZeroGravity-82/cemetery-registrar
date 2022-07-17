<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface CauseOfDeathRepository extends Repository
{
    /**
     * Checks that a cause of death with the same name already exists.
     *
     * @param CauseOfDeath $causeOfDeath
     *
     * @return bool
     */
    public function doesSameNameAlreadyUsed(CauseOfDeath $causeOfDeath): bool;
}
