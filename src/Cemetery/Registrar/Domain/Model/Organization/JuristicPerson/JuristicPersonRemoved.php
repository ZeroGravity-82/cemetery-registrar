<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\Event;
use Cemetery\Registrar\Domain\Model\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonRemoved extends Event
{
    public function __construct(
        private JuristicPersonId $juristicPersonId,
        private Name             $juristicPersonName,
        private Inn              $juristicPersonInn,
    ) {
        parent::__construct();
    }

    public function juristicPersonId(): JuristicPersonId
    {
        return $this->juristicPersonId;
    }

    public function juristicPersonName(): Name
    {
        return $this->juristicPersonName;
    }

    public function juristicPersonInn(): Inn
    {
        return $this->juristicPersonInn;
    }
}
