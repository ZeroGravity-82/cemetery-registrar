<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Deceased\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\EntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathFactory extends EntityFactory
{
    /**
     * @param string $description
     *
     * @return CauseOfDeath
     */
    public function create(
        string $description,
    ): CauseOfDeath {
        $description = new CauseOfDeathDescription($description);

        return (new CauseOfDeath(
            new CauseOfDeathId($this->identityGenerator->getNextIdentity()),
            $description,
        ));
    }
}
