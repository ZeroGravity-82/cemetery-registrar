<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\JuristicPerson;

use Cemetery\Registrar\Domain\Model\AbstractEvent;
use Cemetery\Registrar\Domain\Model\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class JuristicPersonCreated extends AbstractEvent
{
    public function __construct(
        private JuristicPersonId $id,
        private Name             $name,
        private ?Inn             $inn,
    ) {
        parent::__construct();
    }

    public function id(): JuristicPersonId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function inn(): ?Inn
    {
        return $this->inn;
    }
}
