<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
final class JuristicPersonRemoved extends AbstractEvent
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
