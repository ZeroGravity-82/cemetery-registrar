<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\CauseOfDeath\CauseOfDeathId;
use Cemetery\Registrar\Domain\Model\Repository;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
interface NaturalPersonRepository extends Repository
{
    /**
     * Counts natural persons associated with the cause of death.
     *
     * @param CauseOfDeathId $causeOfDeathId
     *
     * @return int
     */
    public function countByCauseOfDeathId(CauseOfDeathId $causeOfDeathId): int;
}
