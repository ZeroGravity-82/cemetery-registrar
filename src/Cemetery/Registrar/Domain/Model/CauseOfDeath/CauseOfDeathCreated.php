<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\CauseOfDeath;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CauseOfDeathCreated extends Event
{
    public function __construct(
        private CauseOfDeathId   $id,
        private CauseOfDeathName $name,
    ) {
        parent::__construct();
    }

    public function id(): CauseOfDeathId
    {
        return $this->id;
    }

    public function name(): CauseOfDeathName
    {
        return $this->name;
    }
}
