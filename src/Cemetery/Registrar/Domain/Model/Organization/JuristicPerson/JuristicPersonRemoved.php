<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemoved extends Event
{
    /**
     * @param JuristicPersonId $juristicPersonId
     */
    public function __construct(
        private readonly JuristicPersonId $juristicPersonId,
    ) {
        parent::__construct();
    }

    /**
     * @return JuristicPersonId
     */
    public function juristicPersonId(): JuristicPersonId
    {
        return $this->juristicPersonId;
    }
}