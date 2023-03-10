<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\AbstractEvent;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRemoved extends AbstractEvent
{
    public function __construct(
        private SoleProprietorId $id,
    ) {
        parent::__construct();
    }

    public function id(): SoleProprietorId
    {
        return $this->id;
    }
}
