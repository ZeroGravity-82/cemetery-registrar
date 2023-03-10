<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemoved extends AbstractEvent
{
    public function __construct(
        private CauseOfDeathId $id,
    ) {
        parent::__construct();
    }

    public function id(): CauseOfDeathId
    {
        return $this->id;
    }
}
