<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\Organization\SoleProprietor;

use Cemetery\Registrar\Domain\Model\Event;
use Cemetery\Registrar\Domain\Model\Organization\Name;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class SoleProprietorRemoved extends Event
{
    public function __construct(
        private SoleProprietorId $soleProprietorId,
        private Name             $soleProprietorName,
        private Inn              $soleProprietorInn,
    ) {
        parent::__construct();
    }

    public function soleProprietorId(): SoleProprietorId
    {
        return $this->soleProprietorId;
    }

    public function soleProprietorName(): Name
    {
        return $this->soleProprietorName;
    }

    public function soleProprietorInn(): Inn
    {
        return $this->soleProprietorInn;
    }
}
