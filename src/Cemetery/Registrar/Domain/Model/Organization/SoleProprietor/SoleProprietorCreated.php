<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Event;
use Cemetery\Registrar\Domain\Model\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorCreated extends Event
{
    public function __construct(
        private SoleProprietorId $id,
        private Name             $name,
        private ?Inn             $inn,
    ) {
        parent::__construct();
    }

    public function id(): SoleProprietorId
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
