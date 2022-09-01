<?php

declare(strict_types=1);

namespace Cemetery\Registrar\Domain\Model\BurialPlace\GraveSite;

use Cemetery\Registrar\Domain\Model\Event;

/**
 * @author Nikolay Ryabkov <ZeroGravity.82@gmail.com>
 */
class CemeteryBlockRemoved extends Event
{
    public function __construct(
        private CemeteryBlockId $cemeteryBlockId,
    ) {
        parent::__construct();
    }

    public function cemeteryBlockId(): CemeteryBlockId
    {
        return $this->cemeteryBlockId;
    }
}
