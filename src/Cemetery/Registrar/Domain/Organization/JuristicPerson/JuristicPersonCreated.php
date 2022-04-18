<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\AbstractEvent;
use Cemetery\Registrar\Domain\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonCreated extends AbstractEvent
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
    public function getJuristicPersonId(): JuristicPersonId
    {
        return $this->juristicPersonId;
    }

    /**
     * @return Name
     */
    public function getJuristicPersonName(): Name
    {
        return $this->juristicPersonName;
    }

    /**
     * @return Inn
     */
    public function getJuristicPersonInn(): Inn
    {
        return $this->juristicPersonInn;
    }
}
