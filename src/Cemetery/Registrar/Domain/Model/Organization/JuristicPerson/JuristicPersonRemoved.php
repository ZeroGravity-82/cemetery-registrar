<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemoved extends AbstractEvent
{
    public function __construct(
        private JuristicPersonId $id,
    ) {
        parent::__construct();
    }

    public function id(): JuristicPersonId
    {
        return $this->id;
    }
}
