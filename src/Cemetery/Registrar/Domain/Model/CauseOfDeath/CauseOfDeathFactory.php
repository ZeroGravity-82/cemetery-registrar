<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\EntityFactory;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathFactory extends EntityFactory
{
    /**
     * @param string $name
     *
     * @return CauseOfDeath
     */
    public function create(
        string $name,
    ): CauseOfDeath {
        $name = new CauseOfDeathName($name);

        return (new CauseOfDeath(
            new CauseOfDeathId($this->identityGenerator->getNextIdentity()),
            $name,
        ));
    }
}
