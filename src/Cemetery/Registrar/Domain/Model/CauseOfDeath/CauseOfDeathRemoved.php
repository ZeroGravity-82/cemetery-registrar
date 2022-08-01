<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemoved extends Event
{
    public function __construct(
        private CauseOfDeathId   $causeOfDeathId,
        private CauseOfDeathName $causeOfDeathName,
    ) {
        parent::__construct();
    }

    public function causeOfDeathId(): CauseOfDeathId
    {
        return $this->causeOfDeathId;
    }

    public function causeOfDeathName(): CauseOfDeathName
    {
        return $this->causeOfDeathName;
    }
}
