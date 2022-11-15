<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AbstractEntityFactory;
use Cemetery\Registrar\Domain\Model\Exception;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathFactory extends AbstractEntityFactory
{
    /**
     * @throws Exception when generating an invalid cause of death ID
     * @throws Exception when the name is invalid
     */
    public function create(
        ?string $name,
    ): CauseOfDeath {
        $name = new CauseOfDeathName((string) $name);

        return (new CauseOfDeath(
            new CauseOfDeathId($this->identityGenerator->getNextIdentity()),
            $name,
        ));
    }
}
