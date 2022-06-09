<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Event;
use Cemetery\Registrar\Domain\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCreated extends Event
{
    /**
     * @param JuristicPersonId $juristicPersonId
     * @param Name             $juristicPersonName
     * @param Inn              $juristicPersonInn
     */
    public function __construct(
        private readonly JuristicPersonId $juristicPersonId,
        private readonly Name             $juristicPersonName,
        private readonly Inn              $juristicPersonInn,
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

    /**
     * @return Name
     */
    public function juristicPersonName(): Name
    {
        return $this->juristicPersonName;
    }

    /**
     * @return Inn
     */
    public function juristicPersonInn(): Inn
    {
        return $this->juristicPersonInn;
    }
}
