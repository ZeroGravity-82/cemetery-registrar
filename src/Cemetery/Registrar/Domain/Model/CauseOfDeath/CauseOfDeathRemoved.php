<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathRemoved extends Event
{
    /**
     * @param CauseOfDeathId   $causeOfDeathId
     * @param CauseOfDeathName $causeOfDeathName
     */
    public function __construct(
        private readonly CauseOfDeathId   $causeOfDeathId,
        private readonly CauseOfDeathName $causeOfDeathName,
    ) {
        parent::__construct();
    }

    /**
     * @return CauseOfDeathId
     */
    public function causeOfDeathId(): CauseOfDeathId
    {
        return $this->causeOfDeathId;
    }

    /**
     * @return CauseOfDeathName
     */
    public function causeOfDeathName(): CauseOfDeathName
    {
        return $this->causeOfDeathName;
    }
}
