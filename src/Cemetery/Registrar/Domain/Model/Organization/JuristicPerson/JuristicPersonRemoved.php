<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemoved extends Event
{
    public function __construct(
        private JuristicPersonId $juristicPersonId,
    ) {
        parent::__construct();
    }

    public function juristicPersonId(): JuristicPersonId
    {
        return $this->juristicPersonId;
    }
}
