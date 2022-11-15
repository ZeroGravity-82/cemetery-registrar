<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AbstractEntityCollection;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathCollection extends AbstractEntityCollection
{
    public function supportedEntityClassName(): string
    {
        return CauseOfDeath::class;
    }
}
