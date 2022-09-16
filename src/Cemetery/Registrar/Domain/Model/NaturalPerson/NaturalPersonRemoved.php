<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\NaturalPerson;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class NaturalPersonRemoved extends Event
{
    public function __construct(
        private NaturalPersonId $naturalPersonId,
    ) {
        parent::__construct();
    }

    public function naturalPersonId(): NaturalPersonId
    {
        return $this->naturalPersonId;
    }
}
